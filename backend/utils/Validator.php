<?php
class Validator {
    
    private $errors = [];

    // Validate required field
    public function required($field, $value, $fieldName = null) {
        if (empty($value) && $value !== '0' && $value !== 0) {
            $name = $fieldName ?: $field;
            $this->errors[$field] = "$name is required";
            return false;
        }
        return true;
    }

    // Validate email
    public function email($field, $value, $fieldName = null) {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $name = $fieldName ?: $field;
            $this->errors[$field] = "$name must be a valid email address";
            return false;
        }
        return true;
    }

    // Validate minimum length
    public function minLength($field, $value, $min, $fieldName = null) {
        if (!empty($value) && strlen($value) < $min) {
            $name = $fieldName ?: $field;
            $this->errors[$field] = "$name must be at least $min characters";
            return false;
        }
        return true;
    }

    // Validate maximum length
    public function maxLength($field, $value, $max, $fieldName = null) {
        if (!empty($value) && strlen($value) > $max) {
            $name = $fieldName ?: $field;
            $this->errors[$field] = "$name must not exceed $max characters";
            return false;
        }
        return true;
    }

    // Validate numeric
    public function numeric($field, $value, $fieldName = null) {
        if (!empty($value) && !is_numeric($value)) {
            $name = $fieldName ?: $field;
            $this->errors[$field] = "$name must be a number";
            return false;
        }
        return true;
    }

    // Validate minimum value
    public function min($field, $value, $min, $fieldName = null) {
        if (!empty($value) && $value < $min) {
            $name = $fieldName ?: $field;
            $this->errors[$field] = "$name must be at least $min";
            return false;
        }
        return true;
    }

    // Validate date format
    public function date($field, $value, $fieldName = null) {
        if (!empty($value)) {
            $d = \DateTime::createFromFormat('Y-m-d', $value);
            if (!$d || $d->format('Y-m-d') !== $value) {
                $name = $fieldName ?: $field;
                $this->errors[$field] = "$name must be a valid date (Y-m-d)";
                return false;
            }
        }
        return true;
    }

    // Get all errors
    public function getErrors() {
        return $this->errors;
    }

    // Check if validation passed
    public function passes() {
        return empty($this->errors);
    }

    // Check if validation failed
    public function fails() {
        return !$this->passes();
    }
}
