<?php

namespace Mytdt\Testing;

use Illuminate\Support\Facades\Validator;
use PHPUnit_Framework_Assert as Assertion;

trait TestTrait
{
    /**
     * Validation errors.
     *
     * @var array
     */
    private $errors = [];

    /**
     * Validate an array against predefined rules.
     *
     * @param array $rules
     * @param array $resourceData
     *
     * @return void
     */
    protected function assertValidArray(array $rules, array $resourceData)
    {
        // validate rules
        $this->validateArray($rules, $resourceData);
        // log errors to console
        if (count($this->errors) > 0) {
            Assertion::fail(implode(PHP_EOL, $this->errors));
        }
    }

    /**
     * Get validation rules and run validator.
     *
     * @param array $rules
     * @param array $resourceData
     *
     * @return void
     */
    protected function validateArray($rules, $resourceData)
    {
        // set all rules to required
        foreach ($rules as $key => $rule) {
            // if the attribute has no children, validate it
            if (!is_array($rule)) {
                $rules[$key] = $rule.'|required';
            // if the attribute has children, do a sub-loop
            } else {
                $rules[$key] = 'required';
                if (array_key_exists($key, $resourceData)) {
                    $this->validateArray($rule, $resourceData[$key]);
                }
            }
        }
        // run validator
        $validator = Validator::make($resourceData, $rules);
        // store errors
        foreach ($validator->messages()->toArray() as $error) {
            $this->errors[] = "\e[1;31m× \033[0m".$error[0];
        }
    }
}
