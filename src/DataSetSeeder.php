<?php


namespace Codeception\Module;

use Codeception\Module;
use PDO;

class DataSetSeeder extends Module {

    /**
     *
     * Inserts the given data set into the database.
     * Use:
     * ```
     * $data_set = [
     *     'table_name' => [
     *         [
     *             'field_name' => 'value',
     *         ],
     *     ],
     * ];
     * $I->seedDatabase($data_set);
     * ```
     *
     * @param $dataSetArray
     * @throws \Codeception\Exception\ModuleException
     */
    public function seedDatabase($dataSetArray, $clearTablesFirst = true) {
        /** @var Db $dbModule */
        $dbModule = $this->getModule('Db');

        /** @var PDO $dbh */
        $dbh = $dbModule->dbh;

        foreach ($dataSetArray as $table => $rows) {
            if ($clearTablesFirst) {
                $delQuery = "DELETE FROM $table";
                $this->debugSection('Delete query', $delQuery);
                $deleteSth = $dbh->query($delQuery);
                $deleteSth->execute();
            }
            
            foreach ($rows as $row) {
                $keys = [];
                $values = [];

                foreach ($row as $key => $value) {
                    $keys[] = $key;
                    $values[] = $value;
                }

                $key_string = implode(',', $keys);
                $value_questionmarks = implode(',', array_map(function () {
                    return '?';
                }, $values));
                $query = "INSERT INTO $table ($key_string) VALUES ($value_questionmarks)";

                $this->debugSection('Query', $query);
                $sth = $dbh->prepare($query);
                $sth->execute($values);
            }
        }
    }


}
