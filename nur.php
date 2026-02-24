#!/usr/bin/env php
<?php
require_once __DIR__ . '/db/dbh.php';

define('C_RESET', "\033[0m");
define('C_GREEN', "\033[32m");
define('C_YELLOW', "\033[33m");
define('C_BLUE', "\033[34m");
define('C_MAGENTA', "\033[35m");
define('C_CYAN', "\033[36m");
define('C_BOLD', "\033[1m");

array_shift($argv);
$cmd = $argv[0] ?? null;

function printHelp() {
    echo C_CYAN . <<<HELP
╭───────────────────────────────╮
│           NurCLI Help         │
╰───────────────────────────────╯

┌─ Commands ────────────────────┐
│                               │
├─ fard <prayer>                │
│   └─ Add the fard rakaas of a │
│      prayer to your tracking  │
│                               │
├─ recite <page_from> <page_to> │
│   └─ Log Qur'an pages that    │
│      you have recited         │
│                               │
├─ extra <raka'a(s)>            │
│   └─ Record extra rakaas you  │
│      prayed beyond fard       │
│                               │
├─ status                       │
│   └─ Show your detailed prayer│
│      and recitation dashboard │
└───────────────────────────────┘

┌─ Examples ────────────────────┐
│                               │
├─ nur fard fajr 2              │
├─ nur recite 5 7               │
├─ nur extra 2                  │
├─ nur status                   │
└───────────────────────────────┘
HELP
. C_RESET . PHP_EOL;
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function progressBar($current, $total, $length = 20) {
    $percent = $total > 0 ? min(1, $current / $total) : 0;
    $filled = round($length * $percent);
    $empty = $length - $filled;
    $bar = str_repeat("█", $filled) . str_repeat("░", $empty);
    return C_YELLOW . "[$bar] " . round($percent * 100) . "%" . C_RESET;
}

function formatTimestamp($ts) {
    return date("Y-m-d H:i", strtotime($ts));
}

switch ($cmd) {
    case 'fard':
        $prayer = $argv[1] ?? null;
        $rakaa = 0;
        if (!$prayer) {
            echo "Usage: nur fard <prayer_name>\n";
            echo "Example: nur fard duhr\n";
            echo "Example: nur fard asr\n";
            echo "Example: nur fard maghrib\n";
            echo "Example: nur fard ishaa\n";
            echo "Example: nur fard fajr\n";
            echo "Example: nur fard sobh\n";
            exit(1);
        }

        if($prayer === 'duhr') {
            $rakaa = 4;
        }
        if($prayer === 'asr') {
            $rakaa = 4;
        }
        if($prayer === 'maghrib') {
            $rakaa = 3;
        }
        if($prayer === 'ishaa') {
            $rakaa = 4;
        }
        if($prayer === 'fajr') {
            $rakaa = 2;
        }
        if($prayer === 'sobh') {
            $rakaa = 2;
        }

        $stmt = $conn->prepare("INSERT INTO prayers (prayer_name, prayer_rakaa) VALUES (?, ?)");
        $stmt->bind_param("si", $prayer, $rakaa);
        echo $stmt->execute() ? C_GREEN . "✅ Added $rakaa rakaas for $prayer.\n" . C_RESET
                              : C_YELLOW . "❌ Failed: " . $stmt->error . C_RESET;
        break;

    case 'recite':
        $from = $argv[1] ?? null;
        $to = $argv[2] ?? null;
        if (!$from || !$to || !is_numeric($from) || !is_numeric($to)) {
            echo "Usage: nur recite <page_from> <page_to>\n";
            exit(1);
        }
        $stmt = $conn->prepare("INSERT INTO recites (page_from, page_to) VALUES (?, ?)");
        $stmt->bind_param("ii", $from, $to);
        echo $stmt->execute() ? C_GREEN . "✅ Logged recitation: pages $from → $to.\n" . C_RESET
                              : C_YELLOW . "❌ Failed: " . $stmt->error . C_RESET;
        break;

    case 'extra':
        $rakaa = $argv[1] ?? null;
        if (!$rakaa || !is_numeric($rakaa)) {
            echo "Usage: nur extra <raka'a(s)>\n";
            exit(1);
        }
        $stmt = $conn->prepare("INSERT INTO prayers (prayer_name, prayer_rakaa) VALUES ('extra', ?)");
        $stmt->bind_param("i", $rakaa);
        echo $stmt->execute() ? C_GREEN . "✅ Logged $rakaa extra rakaas.\n" . C_RESET
                              : C_YELLOW . "❌ Failed: " . $stmt->error . C_RESET;
        break;

    case 'status':
        echo C_CYAN . "╭────────────── NurCLI Dashboard ──────────────╮\n" . C_RESET;

        $res = $conn->query("SELECT SUM(prayer_rakaa) AS total, MAX(prayed_at) AS last FROM prayers WHERE prayer_name != 'extra'");
        $prayer = $res->fetch_assoc();
        $prayerTotal = $prayer['total'] ?? 0;
        $prayerLast = $prayer['last'] ?? '-';

        $res = $conn->query("SELECT SUM(prayer_rakaa) AS total, MAX(prayed_at) AS last FROM prayers WHERE prayer_name='extra'");
        $extra = $res->fetch_assoc();
        $extraTotal = $extra['total'] ?? 0;
        $extraLast = $extra['last'] ?? '-';

        $res = $conn->query("SELECT SUM(page_to-page_from+1) AS total, MAX(recited_at) AS last FROM recites");
        $recite = $res->fetch_assoc();
        $totalPages = $recite['total'] ?? 0;
        $lastRecited = $recite['last'] ?? '-';

        $res = $conn->query("SELECT COUNT(*) AS sessions FROM prayers WHERE prayer_name != 'extra'");
        $sessions = $res->fetch_assoc()['sessions'] ?? 0;

        echo C_BOLD . "├─ Fard Rakaas  : " . C_GREEN . $prayerTotal . C_RESET . " " . progressBar($prayerTotal, 50) 
             . " (Last: " . formatTimestamp($prayerLast) . ")\n" . C_RESET;

        echo C_BOLD . "├─ Extra Rakaas : " . C_MAGENTA . $extraTotal . C_RESET . " " . progressBar($extraTotal, 20) 
             . " (Last: " . formatTimestamp($extraLast) . ")\n" . C_RESET;

        echo C_BOLD . "├─ Pages Recited: " . C_BLUE . $totalPages . C_RESET . " " . progressBar($totalPages, 604) 
             . " (Last: " . formatTimestamp($lastRecited) . ")\n" . C_RESET;

        echo C_BOLD . "├─ Sessions     : " . C_YELLOW . $sessions . C_RESET . "\n";

        $avgPerSession = $sessions > 0 ? round($prayerTotal / $sessions, 2) : 0;
        echo C_BOLD . "└─ Avg Fard/Session: " . C_CYAN . $avgPerSession . C_RESET . "\n";

        echo C_CYAN . "╰───────────────────────────────────────────────╯\n" . C_RESET;
        break;

    case 'help':
    case null:
        printHelp();
        break;

    default:
        echo C_YELLOW . "❌ Unknown command: $cmd\n\n" . C_RESET;
        printHelp();
        exit(1);
}

$conn->close();
