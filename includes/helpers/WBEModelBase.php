<?php

namespace WPBackendDash\Helpers;

class WBEModelBase {
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $attributes = [];
    protected $required = [];

    public function __construct() {
        if ($this->table) {
            global $wpdb;
            $this->table = $wpdb->prefix . $this->table;
        }
    }

    public function __get($name) {
        return $this->attributes[$name] ?? null;
    }

    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }

    public function fill(array $data) {
        foreach ($data as $key => $value) {
            $this->attributes[$key] = $value;
        }
        return $this;
    }

    public function all() {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM {$this->table}");
    }

    public function getRequired() {
        return $this->required;
    }
    
    public static function find($id) {
        global $wpdb;
        $model = new static();
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$model->table} WHERE {$model->primaryKey} = %d", $id),
            ARRAY_A
        );
        if ($row) {
            return (new static())->fill($row);
        }
        return null;
    }

    public static function where($column, $value) {
        global $wpdb;
        $model = new static();
        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$model->table} WHERE {$column} = %s", $value),
            ARRAY_A
        );

        return array_map(function ($row) {
            return (new static())->fill($row);
        }, $results);
    }

    public function create(array $data) {
        global $wpdb;

        $insertData = [];
        foreach ($this->fillable as $field) {
            if (isset($data[$field])) {
                $insertData[$field] = $data[$field];
            }
        }

        $wpdb->insert($this->table, $insertData);
        return $this->find($wpdb->insert_id);
    }

    public function save() {

        if (empty($this->fillable)) {
            throw new \Exception("No fillable fields defined for model.");
        }

        if ($this->exists()) {
            // Update existing record
            return $this->update($this->getKey(), $this->attributes);
        } else {
            // Create new record
            return $this->create($this->attributes);
        }
    }

    public function getKey() {
        return isset($this->attributes[$this->primaryKey]) ? $this->attributes[$this->primaryKey] : null;
    }

    public function exists($id = null) {
        global $wpdb;

        $id = $id ?? $this->getKey();

        if (!$id) {
            return false;
        }

        $count = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$this->primaryKey} = %d", $id)
        );
        return $count > 0;
    }

    public function update($id, array $data) {
        global $wpdb;

        $updateData = [];
        foreach ($this->fillable as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        $wpdb->update(
            $this->table,
            $updateData,
            [ $this->primaryKey => $id ]
        );

        return $this->find($id);
    }

    public function delete($id) {
        global $wpdb;
        return $wpdb->delete($this->table, [ $this->primaryKey => $id ]);
    }

    public function toArray() {
        return $this->attributes;
    }
}
