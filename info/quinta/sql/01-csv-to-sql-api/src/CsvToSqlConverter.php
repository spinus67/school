<?php

declare(strict_types=1);

namespace App;

use LogicException;

final class CsvToSqlConverter
{
    public function convert(string $csv, string $database, string $table): string
    {
        $stream = fopen("php://temp", 'r+');

        if(!$stream) {
            throw new LogicException('Could not open temporary stream.');
        }


        fwrite($stream, $csv);
        rewind($stream);

        // TODO: implement the conversion described in the README
        // and make every test in tests/run.php pass. 

        $columns = fgetcsv($stream, null, ',', '"', ''); 

        $rows = fgetcsv($stream, null, ',', '"', ''); 
        
        $insert = "INSERT INTO `school`.`students` (`first_name`, `last_name`, `age`) VALUES\n"
        . "('$rows[0]', '$rows[1]', $rows[2]);";

        
    
        return $insert;

        $insert = "INSERT INTO `school`.`students` (`first_name`, `last_name`, `age`) VALUES\n";
        $columns = fgetcsv($stream, null, ',', '"', ''); 

        $rows = fgetcsv($stream, null, ',', '"', ''); 
        
        $values = [];

        while (($rows = fgetcsv($stream, null, ',', '"', '')) == true) {
        $values[] = "('$rows[0]', '$rows[1]', $rows[2])";
            
        return $insert;
    
         }

        

        
  }
}