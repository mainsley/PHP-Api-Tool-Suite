<?php

include_once(dirname(__DIR__)."/Job/JobPost.php");         //Call Specifc//Incomplete//

class JobContentUpdate extends JobPost {

    /*
     * Stats type
     *
     * @var array
     */
    protected $job = "content_update";

    /*
     * Valid Parameters for a call
     *
     * @var array
     */
    protected $description = "Update or create any number of Products or Articles.";


    /*
     * cli parameters specific to this Job.
     * These will overwrite any existing keys in Parent Call method, but not
     * in the base api call abstract class. (This is because I want api key 
     * and secret to be the first params printed in the help menu.) So don't 
     * add stuff to the cli_params var.
     *
     * @var array
     */
    private $cli_params__job = [
        //returned_var => ["cli_entry_name", "Description"]
        "file" => ["file","File: Local path to a csv file with user information. Will be automatically split up and uploaded in chunks."],
        // "file_type" => ["file_type", "File Type: JSON or CSV. "],
        "brand_name" => ["brand", "Brand: A human readable name of the client. Used for folder creation when uploading files."],
        // "" => ["",""],
    ];

    /*
     * Any Flags specific to this call. 
     *
     * @var array
     */
    private $cli_options__job = [
        //returned_flag => ["cli_entry_name", "Description"]
        "-j" => ["isJobDescription","return more information about this job."],
        "-a" => ["isValidateFile", "skip checks on file before upload."]
        // "" => [],
    ];

    /*
     * Allow flags set here to effect other flags as well as themselves.
     *
     * @var array
     */
    private $cli_options_modifications__job = [
        "-j" => ["isHelp", true],
    ];

    /*
     * Dependencies a parameter requires to function. As dependencies are often inter-related,
     * we need a structure to show that relationship. I don't like my solution, but oh well. 
     *
     * The main key is the parameter that has dependencies. The array holds sub collections. 
     * The "always_required" sub array will always be validated against. The other arrays will only
     * be checked against, if the key is not present. 
     *
     * All keys are the api var names, or the final var names. Do not use the cli names. 
     *
     * @var array
     */
    private $api_params_validation__job = [
        //api_param => ["negation_param" => ["dependency_1", "dependency_2"], "always_required" => ["dependency_3"]],
        // "file" => ["always_required" => ["file_type"]],
        // "" => [],
    ];

    /*
     * Gives the ability to input arrays in the command line as individual the individual members.
     * Make sure to specify the prefix in the params description
     *
     * @var array
     */
    protected $api_params_structure__job = [
        //"returned_var_array_name" => "prefix_name"

    ];

    public function __construct() {
        $this->api_vars["job"] = $this->job;
        parent::__construct();
    }
    
    public function ingestInput($vars, $skipValidate = false) {
        parent::ingestInput($vars, $skipValidate);
        //Prep file upload data
        if (isset($this->api_vars["file"])) { 
            $this->method = "uploadFile";
            $this->endpoint = $this->job;
            if (!isset($this->api_vars["brand_name"])) {
                $this->api_vars["brand_name"] = $this->account->getName();
                if ($this->api_vars["brand_name"] == null) {
                    CliScriptAbstract::confirm("You need to provide a brand if you are not using a preconfigured account.\nContinue anyway?","Add the '-h' option for more details on valid inputs."); 
                    $this->api_vars["brand_name"] = "unknown";                
                }
            }
            if (CliScriptAbstract::$flags["isValidateFile"]) {
                $this->api_vars["is_skip_check"] = true;
            }
            if (!isset($this->api_vars["file_type"])) {
                $this->api_vars["file_type"] = "json";
            }
        } else {
            echo "This isn't supported. How did you get here?\n\nSERIOUSLY!!!???\n\n";
        }
    }

    public function getMethod() {
        if (!isset($this->method)) {
            $this->method = "postCall";
        }
        return parent::getMethod($this->method);
    }

//helper methods

    public function useQueryCLI() {
        parent::useQueryCLI();
    }

    public function getOtherInputsDescription() {
        if (CliScriptAbstract::$flags["isJobDescription"]) {
            return parent::getOtherInputsDescription($fd);
        }
        return parent::getOtherInputsDescription();
    }

    public function getCliParameters($child_params = null) {
        //I'm reversing the array so I can have later classes overwrite earlier ones, but the parent classes still display first.
        if ($child_params != null) {
            $cli_params = $child_params + array_reverse($this->cli_params__job);
        } else {
            $cli_params = array_reverse($this->cli_params__job);
        }
        return parent::getCliParameters($cli_params);
    }

    public function getApiParamValidation($child_param_validation = null) {
        if ($child_param_validation != null) {
            $params_validation = $child_param_validation + $this->api_params_validation__job;
        } else {
            $params_validation = $this->api_params_validation__job;
        }
        return parent::getApiParamValidation($params_validation);
    }

    public function getApiParamStructure($child_params_structure = null) {        
        if ($child_params_structure != null) {
            $params_structure = $child_params_structure + $this->api_params_structure__job;
        } else {
            $params_structure = $this->api_params_structure__job;
        }
        return parent::getApiParamStructure($params_structure);
    }

    public function getCliOptions($child_options = null) {
        if ($child_options != null) {
            $cli_options = $child_options + $this->cli_options__job;
        } else {
            $cli_options = $this->cli_options__job;
        }
        return parent::getCliOptions($cli_options);
    }

    public function getFlagModifications($child_modifications = null) {
        if ($child_modifications != null) {
            $option_modifications = $child_modifications + $this->cli_options_modifications__job;
        } else {
            $option_modifications = $this->cli_options_modifications__job;
        }
        return $option_modifications;
    }

    public function getCallData() {
        return parent::getCallData();
    }

}