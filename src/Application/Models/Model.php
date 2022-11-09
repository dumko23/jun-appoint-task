<?php

namespace App\Application\Models;

use App\Application\Database\QueryBuilder;
use Exception;
use PDO;

class Model
{
    protected QueryBuilder $db;

    public function __construct(PDO $pdo)
    {
        $this->db = new QueryBuilder($pdo);
    }


    /**
     * Add data to table (mentioned in config)
     *
     * @param  array  $data  data to add
     * @param  string  $dbTable
     * @return array
     */
    public function add(array $data, string $dbTable): array
    {
        return $this->db->insertToDB(
            $dbTable,
            $data
        );
    }

    /**
     * Update data in DB
     *
     * @param  array  $data               new data
     * @param  string  $whereStatement    column name
     * @param  array  $searchedStatement  row that matches "where" statement
     * @param  string  $dbTable
     * @return array
     */

    public function update(array $data, string $whereStatement, array $searchedStatement, string $dbTable): array
    {
        return $this->db->updateDB(
            $dbTable,
            $data,
            $whereStatement,
            $searchedStatement
        );
    }

    /**
     * Adding error definition to array that will be given as response
     *
     * @param  array  $errorList
     * @param  string  $name
     * @param  string  $message
     * @return array
     */
    public function addError(array $errorList, string $name, string $message): array
    {
        $errorList[$name] = $message;
        return $errorList;
    }

    /**
     * Forming fetch call to DB
     *
     * @param  string  $select        selectable data
     * @param  string  $dbAndTable
     * @param  string  $where         column name (optional, for specific data fetch)
     * @param  string  $searchedItem  row that matches "where" statement (optional, for specific data fetch)
     * @return array
     * @throws Exception
     */
    public function getData(string $select, string $dbAndTable, string $where = '', string $searchedItem = ''): array
    {
        return $this->db->getFromDB($select, $dbAndTable, $where, $searchedItem);
    }

    /**
     * Validation method. checks the correctness of the provided data according to the given rules.
     * Keys of "rules" array should match keys of "record" array
     *
     * @param  array  $record  array of data to check
     * @param  array  $rules   array of rules
     * @return bool|array
     */
    public function validation(array $record, array $rules): bool|array
    {
        $errors = [];

        foreach ($rules as $fieldName => $rule) {
            if (str_contains($rule, 'required') && $record[$fieldName] === '') {
                $errors = $this->addError($errors, $fieldName, 'Input is empty!');
            }
            if (str_contains($rule, 'maxlength')) {
                preg_match('/(?<=maxlength:)(\d+)(?=\|)/U', $rule, $matches, PREG_OFFSET_CAPTURE);
                if (strlen($record[$fieldName]) > intval($matches[0][0])) {

                    $errors = $this->addError(
                        $errors,
                        $fieldName,
                        "Input length should be maximum {$matches[0][0]} symbols!"
                    );
                }
            }
            if (str_contains($rule, 'minlength')) {
                preg_match('/(?<=minlength:)(\d+)(?=\|)/U', $rule, $matches, PREG_OFFSET_CAPTURE);
                if (strlen($record[$fieldName]) < intval($matches[0][0])) {
                    $errors = $this->addError(
                        $errors,
                        $fieldName,
                        "Input length should be minimum {$matches[0][0]} symbols!"
                    );
                }
            }
            if (
                str_contains($rule, 'emailFormat')
                && filter_var($record[$fieldName], FILTER_VALIDATE_EMAIL) === false
            ) {
                $errors = $this->addError($errors, $fieldName, 'Incorrect email format!');
            }
            if (
                str_contains($rule, 'unique')
                && isset($this->getData(
                        'id',
                        $_ENV['DB_DATABASE'] . '.' . $_ENV['DB_USERS_TABLE'],
                        'where email = ',
                        $record[$fieldName]
                    )['data'][0])
            ) {
                $errors = $this->addError($errors, $fieldName, 'This email is already registered!');
            }
        }
        if (count($errors) === 0) {
            return [
                'result' => true
            ];
        } else {
            $result = [
                'result' => false,
                'error' => $errors
            ];
            unset($errors);
            return $result;
        }
    }

    /**
     * Delete data from table by given condition
     *
     * @param  string  $where             column name to search data
     * @param  array  $searchedStatement  row that matches "where" statement
     * @param  string  $dbTable
     * @return array
     */
    public function delete(string $where, array $searchedStatement, string $dbTable): array
    {
        return $this->db
            ->delete(
                $dbTable,
                $where,
                $searchedStatement
            );
    }
}
