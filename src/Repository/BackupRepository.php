<?php

namespace App\Repository;

use function mysqli_query;

class BackupRepository
{
    // Stats:
    // Old: 239.9MB in 11 minutes
    // New: 182.6MB in 44 seconds (uses bulk inserts)

    private $date;
    private $tables = [];
    private $fileName;
    private $server;
    private static $_db;
    const CHUNK_SIZE = 100;

    public function __construct()
    {
        $b = parse_url(getenv('DATABASE_URL'));
        self::$_db = mysqli_connect($b['host'], $b['user'], $b['pass'], trim($b['path'], '/'));
        if (!self::$_db) {
            die("Cannot connect to database!");
        }
    }

    /**
     * @param bool $structure
     * @param bool $tableNames
     * @return string
     */
    public function generate($structure = true, $tableNames = false)
    {
        set_time_limit(600);    // Extend maximum execution time to 10 mins
        $this->date =       time();
        $this->fileName =   strftime('%Y%m%d_%H%M', $this->date).".sql";
        $this->server =     getenv("SERVER_NAME");

        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $this->fileName);

        $this->getTableAndColumnSpecs($tableNames);
        $this->drawFileHeader();
        if ($structure) {
            $this->drawTableCreate();
        }
        $this->drawData();
        die;
    }

    /**
     * @return void
     */
    private function drawData()
    {
        print
            "# ************************************\n"
            ."# * Table Data:                      *\n"
            ."# ************************************\n"
            ."\n";

        foreach ($this->tables as $table => $columns) {
            $this->drawTableData($table, $columns);
        }
        print
            "#\n"
            ."# (End of table data)\n"
            ."#\n"
            ."\n";
    }

    /**
     * @param $filename
     * @return void
     */
    private function drawFileHeader()
    {
        print
            "# ".str_repeat('*', 50)."\n"
            . "# * " . str_pad('RNA / REU / RWW Database Export Dump', 46, ' ')." *\n"
            . "# ".str_repeat('*', 50)."\n"
            . "# * Filename:  ".str_pad($this->fileName, 35, ' ')." *\n"
            . "# * System:    ".str_pad($this->server, 35, ' ')." *\n"
            . "# * Date:      ".str_pad(strftime('%A %Y-%m-%d at %H:%M:%S', $this->date), 35, ' ')." *\n"
            . "# ".str_repeat('*', 50)."\n"
            . "\n";
    }

    private function drawTableData($table, $columns)
    {
        $sql =      "SELECT * FROM `$table` ORDER BY `ID`";
        $result =   mysqli_query(self::$_db, $sql);
        $count =    mysqli_num_rows($result);
        $chunkHeader = "INSERT INTO `$table`\n  (`" . implode('`, `', array_keys($columns)) . "`)\nVALUES\n";
        $items = 0;
        print
            "# ".str_repeat('*', 50)."\n"
            . "# * " . str_pad($table . " ($count records)", 46, ' ')." *\n"
            . "# ".str_repeat('*', 50)."\n";
        for ($i = 0; $i < $count; $i += $this::CHUNK_SIZE) {
            print $chunkHeader;
            for ($j = 0; $j<$this::CHUNK_SIZE; $j++) {
                $lastItem = ($j + 1 >= $this::CHUNK_SIZE) || ($i + $j + 1 >= $count);
                if ($items < $count) {
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $line = [];
                    foreach($row as $field => $data) {
                        $quoted =   $columns[$field];
                        $line[] =   ($quoted ? "'" . addslashes($data) . "'" : $data);
                    }
                    print "  (".implode(', ', $line).")" .($lastItem ? ";\n\n" : ",\n");
                    $items++;
                }
            }
        }
    }

    private function drawTableCreate()
    {
        print
            "# ************************************\n"
            . "# * Table Structures:                *\n"
            . "# ************************************\n";
        foreach ($this->tables as $table => $result) {
            $sql =      "SHOW CREATE TABLE `$table`";
            $result =   $this->getRow($sql);
            $create =   $result['Create Table'];
            print "DROP TABLE IF EXISTS `" . $table . "`;\n" . $create . ";\n\n";
        }
        print
            "# ************************************\n"
            . "# * (End of Table Structures)        *\n"
            . "# ************************************\n"
            . "\n";
    }

    private function getTableAndColumnSpecs($tableNames = false)
    {
        if (!$tableNames) {
            $sql =      "SHOW TABLE STATUS";
            $results =  $this->getRows($sql);
            foreach ($results as $result) {
                $this->tables[$result['Name']] =    [];
            }
        } else {
            $tableNamesArray =    explode(',', str_replace(' ', '', $tableNames));
            foreach ($tableNamesArray as $name) {
                $this->tables[$name] =    [];
            }
        }
        foreach ($this->tables as $table => $columns) {
            $sql =      "SHOW COLUMNS FROM `$table`";
            $results =  $this->getRows($sql);
            foreach ($results as $column) {
                $type = explode('(', $column['Type']);
                switch ($type[0]) {
                    case 'tinyint':
                    case 'smallint':
                    case 'mediumint':
                    case 'int':
                    case 'bigint':
                    case 'float':
                    case 'double':
                    case 'decimal':
                        $quote =    0;
                        break;
                    default:
                        $quote =    1;
                        break;
                }
                $this->tables[$table][$column['Field']] = $quote;
            }
        }
    }

    private function getField($sql)
    {
        $result = mysqli_query(self::$_db, $sql);
        return $result->fetch_array(MYSQLI_NUM)[0];
    }

    private function getRow($sql, $type = MYSQLI_ASSOC)
    {
        $result = mysqli_query(self::$_db, $sql);
        return $result->fetch_array($type);
    }

    private function getRows($sql, $type = MYSQLI_ASSOC)
    {
        $result = mysqli_query(self::$_db, $sql);
        $rows = [];
        while ($row = $result->fetch_array($type)) {
            $rows[] = $row;
        }
        return $rows;
    }
}
