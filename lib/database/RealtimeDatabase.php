<?php

namespace Bolt\Lib\Database;

use Bolt\Utils\UnixTime;

class RealtimeDatabase
{
    static function init()
    {
        header("Cache-Control: no-store");
        header("Content-Type: text/event-stream");
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        ini_set('output_buffering', 'off');
        ini_set('zlib.output_compression', 'off');
        ini_set('implicit_flush', 'on');

        ob_implicit_flush(true);

        if (ob_get_level()) ob_end_flush();

        // Initialize EventSource
        echo PHP_EOL;
        flush();
    }

    static function getStreamContentType()
    {
        return "text/event-stream";
    }

    static function sendMsg(mixed $msg): void
    {
        $id = UnixTime::getCurrentTimeByMilliseconds();

        echo "id: $id" . PHP_EOL;
        echo "data: $msg" . PHP_EOL;
        echo PHP_EOL;
        flush();

        // Conditional flush to avoid excessive flushing overhead
        if (connection_aborted() === 0) {
            flush();
        }
    }

    static function listenTable(RecordOperations $table)
    {
        $lastUpdate = self::getLastUpdateTime($table);

        while (true) {
            if (connection_aborted()) {
                break;
            }

            $newRecords = $table->getFilteredList("updated > $lastUpdate", "id, updated");

            if (!empty($newRecords)) {
                self::sendMsg(json_encode([
                    'action' => 'update',
                ]));

                $lastUpdate = max(array_column($newRecords, 'updated'));
            }

            usleep(500000);
        }
    }

    static function listenRecord(string $id, RecordOperations $table)
    {
        $lastUpdate = self::getRecordLastUpdateTime($table, $id);

        while (true) {
            if (connection_aborted()) {
                break;
            }

            $newRecord = $table->getFilteredList("id = $id AND updated > $lastUpdate", "id, updated");

            if (!empty($newRecord)) {
                self::sendMsg(json_encode([
                    'action' => 'update',
                ]));

                $lastUpdate = $newRecord[0]['updated'];
            }

            usleep(500000);
        }
    }

    private static function getLastUpdateTime(RecordOperations $table): int
    {
        $lastRecord = $table->getFilteredList("1 ORDER BY updated DESC LIMIT 1", "updated");
        return $lastRecord[0]['updated'] ?? 0;
    }

    private static function getRecordLastUpdateTime(RecordOperations $table, string $id): int
    {
        $record = $table->getOne("id = $id", "updated");
        return $record['updated'] ?? 0;
    }
}
