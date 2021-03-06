<?php

namespace PhpAjaxFormDemo\Forms;

use PhpAjaxFormDemo\Data\MultiForeignRecord;
use PhpAjaxFormDemo\Forms\AjaxForm;
use PhpAjaxFormDemo\Data\Record;
use PhpAjaxFormDemo\Data\SingleForeignRecord;

/**
 * AJAX form example class: record read (no submission expected)
 * 
 * @package ajax-form-demo
 * 
 * @author Juan Carrión
 * 
 * @version 0.0.1
 */

class RecordRead extends AjaxForm
{

    /**
     * Initialize specific form constants
     */
    private const FORM_ID = 'record-read';
    private const FORM_NAME = 'Read record';
    private const TARGET_OBJECT_NAME = 'Record';
    private const SUBMIT_URL = APP_URL . '/form-manager-record-read.php';
    private const EXPECTED_SUBMIT_METHOD = AjaxForm::HTTP_GET;

    /**
     * Constructs the form object
     */
    public function __construct()
    {
        parent::__construct(
            self::FORM_ID,
            self::FORM_NAME,
            self::TARGET_OBJECT_NAME,
            self::SUBMIT_URL,
            self::EXPECTED_SUBMIT_METHOD
        );

        $this->setReadOnlyTrue();
    }

    protected function getDefaultData(array $requestData) : array
    {
        // Check that uniqueId was provided
        if (! isset($requestData['uniqueId'])) {
            $responseData = array(
                'status' => 'error',
                'error' => 400, // Bad request
                'messages' => array(
                    'Missing param "uniqueId".'
                )
            );

            return $responseData;
        }

        $uniqueId = $requestData['uniqueId'];

        // Check that uniqueId is valid
        if (! Record::existsById($uniqueId)) {
            $responseData = array(
                'status' => 'error',
                'error' => 404, // Not found
                'messages' => array(
                    'Invalid param "uniqueId".'
                )
            );

            return $responseData;
        }

        $record = Record::getById($uniqueId);

        // Nationality HATEOAS formalization
        $nationalityLink = AjaxForm::generateHateoasSelectLink(
            'nationality',
            'single',
            $record->getNationality()
        );

        // Hobbies HATEOAS formalization
        $hobbiesLink = AjaxForm::generateHateoasSelectLink(
            'hobbies',
            'multi',
            $record->getHobbies()
        );

        // Map data to match placeholder inputs' names
        $responseData = array(
            'status' => 'ok',
            'links' => array(
                $nationalityLink,
                $hobbiesLink
            ),
            self::TARGET_OBJECT_NAME => $record
        );

        return $responseData;
    }

    public function generateFormInputs() : string
    {
        $html = <<< HTML
        <input type="hidden" name="uniqueId">
        <div class="form-group">
            <label>Name</label>
            <input name="name" type="text" class="form-control" placeholder="Name" disabled="disabled">
        </div>
        <div class="form-group">
            <label>Surname</label>
            <input name="surname" type="text" class="form-control" placeholder="Surname" disabled="disabled">
        </div>
        <div class="form-group">
            <label>Nationality</label>
            <select name="nationality" class="form-control" id="control-nationality" disabled="disabled">
            </select>
        </div>
        <div class="form-group">
            <label>Hobbies</label>
            <select name="hobbies" class="form-control" id="control-hobbies" multiple="multiple" disabled="disabled">
            </select>
        </div>
        HTML;

        return $html;
    }
}

?>