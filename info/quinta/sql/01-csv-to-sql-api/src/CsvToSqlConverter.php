<?php

declare(strict_types=1);

namespace App;

use LogicException;
use InvalidArgumentException;


final class CsvToSqlConverter
{
    public function convert(string $csv, string $database, string $table): string
    {
        $stream = fopen("php://temp", 'r+');

        if (!$stream) {
            throw new LogicException('Could not open temporary stream.');
        }

        fwrite($stream, $csv);
        rewind($stream);

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $database)) {
            throw new InvalidArgumentException('Invalid database name.');
        }
        
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            throw new InvalidArgumentException('Invalid table name.');
        }
         
        $columns = fgetcsv($stream, null, ',', '"', '');
        if ($columns === false) {
            throw new InvalidArgumentException('CSV is empty.');
        }
        if (in_array('', $columns, true)) {
            throw new InvalidArgumentException('Columns cannot be empty.');
        }
        
        if (count($columns) !== count(array_unique($columns))) {
            throw new InvalidArgumentException('Columns cannot be duplicated.');
        }
        if ($columns === false) {
            throw new InvalidArgumentException('CSV is empty.');
        }
        
        foreach ($columns as $column) {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
                throw new InvalidArgumentException('Invalid column name.');
            }
        }

        $values = [];
 
        while (($rows = fgetcsv($stream, null, ',', '"', '')) !== false) {
            if ($rows === [null]) {
                continue;
            }
            if (count($rows) !== count($columns)) {
                throw new InvalidArgumentException('Wrong number of values.');
            }
            
            $values[] = '(' . implode(', ', array_map(function ($value) {
                if ($value === '') {
                    return 'NULL';
                }
        
                if (is_numeric($value) && !preg_match('/^0\d+$/', $value)) {
                    return $value;
                }
        
                return "'" . str_replace("'", "''", $value) . "'";

            }, $rows)) . ')';
        }

        if (empty($values)) {
            throw new InvalidArgumentException('CSV has no data rows.');
        }
        

        $insert = "INSERT INTO `$database`.`$table` (`"
            . implode('`, `', $columns)
            . "`) VALUES\n"
            . implode(",\n", $values)
            . ";";

        return $insert;
    }
}
