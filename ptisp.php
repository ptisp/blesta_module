<?php

//v0.0.1

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "apis" . DIRECTORY_SEPARATOR . "RestRequest.inc.php";

/**
 * PTisp Module
 */
class Ptisp extends Module {

	/**
	 * @var string The version of this module
	 */
	private static $version = "1.0.1";
	/**
	 * @var string The authors of this module
	 */
	private static $authors = array(array('name'=>"PTisp",'url'=>"https://www.ptisp.com"));

	/**
	 * Initializes the module
	 */
	public function __construct() {
		// Load components required by this module
		Loader::loadComponents($this, array("Input"));

		// Load the language required by this module
		Language::loadLang("ptisp", null, dirname(__FILE__) . DS . "language" . DS);

		Configure::load("ptisp", dirname(__FILE__) . DS . "config" . DS);
	}

	/**
	 * Returns the name of this module
	 *
	 * @return string The common name of this module
	 */
	public function getName() {
		return Language::_("PTisp.name", true);
	}

	/**
	 * Returns the version of this module
	 *
	 * @return string The current version of this module
	 */
	public function getVersion() {
		return self::$version;
	}

	/**
	 * Returns the name and URL for the authors of this module
	 *
	 * @return array A numerically indexed array that contains an array with key/value pairs for 'name' and 'url', representing the name and URL of the authors of this module
	 */
	public function getAuthors() {
		return self::$authors;
	}

	/**
	 * Returns the value used to identify a particular service
	 *
	 * @param stdClass $service A stdClass object representing the service
	 * @return string A value used to identify this service amongst other similar services
	 */
	public function getServiceName($service) {
		foreach ($service->fields as $field) {
			if ($field->key == "domain-name")
				return $field->value;
		}
		return null;
	}

	/**
	 * Returns a noun used to refer to a module row (e.g. "Server", "VPS", "Reseller Account", etc.)
	 *
	 * @return string The noun used to refer to a module row
	 */
	public function moduleRowName() {
		return Language::_("PTisp.module_row", true);
	}

	/**
	 * Returns a noun used to refer to a module row in plural form (e.g. "Servers", "VPSs", "Reseller Accounts", etc.)
	 *
	 * @return string The noun used to refer to a module row in plural form
	 */
	public function moduleRowNamePlural() {
		return Language::_("PTisp.module_row_plural", true);
	}

	/**
	 * Returns a noun used to refer to a module group (e.g. "Server Group", "Cloud", etc.)
	 *
	 * @return string The noun used to refer to a module group
	 */
	public function moduleGroupName() {
		return null;
	}

	/**
	 * Returns the key used to identify the primary field from the set of module row meta fields.
	 * This value can be any of the module row meta fields.
	 *
	 * @return string The key used to identify the primary field from the set of module row meta fields
	 */
	public function moduleRowMetaKey() {
		return "registrar";
	}

	/**
	 * Returns the value used to identify a particular package service which has
	 * not yet been made into a service. This may be used to uniquely identify
	 * an uncreated services of the same package (i.e. in an order form checkout)
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param array $vars An array of user supplied info to satisfy the request
	 * @return string The value used to identify this package service
	 * @see Module::getServiceName()
	 */
	public function getPackageServiceName($packages, array $vars=null) {
		if (isset($vars['domain-name']))
			return $vars['domain-name'];
		return null;
	}

	/**
	 * Attempts to validate service info. This is the top-level error checking method. Sets Input errors on failure.
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param array $vars An array of user supplied info to satisfy the request
	 * @return boolean True if the service validates, false otherwise. Sets Input errors when false.
	 */
	public function validateService($package, array $vars=null) {
		return true;
	}

	/**
	 * Adds the service to the remote server. Sets Input errors on failure,
	 * preventing the service from being added.
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param array $vars An array of user supplied info to satisfy the request
	 * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
	 * @param stdClass $parent_service A stdClass object representing the parent service of the service being added (if the current service is an addon service and parent service has already been provisioned)
	 * @param string $status The status of the service being added. These include:
	 * 	- active
	 * 	- canceled
	 * 	- pending
	 * 	- suspended
	 * @return array A numerically indexed array of meta fields to be stored for this service containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function addService($package, array $vars=null, $parent_package=null, $parent_service=null, $status="pending") {

		$row = $this->getModuleRow($package->module_row);

		$username = $row->meta->reseller_id;
		$password = $row->meta->key;
		$nic = $row->meta->epp;

		if (isset($vars['domain-name']))
			$tld = $this->getTld($vars['domain-name'], true);

		if ($package->meta->type == "domain") {
			$contact_fields = Configure::get("PTisp.contact_fields");
			$customer_fields = Configure::get("PTisp.customer_fields");
			$domain_field_basics = array('years' => true, 'ns' => true, 'customer-id' => true, 'reg-contact-id' => true, 'admin-contact-id' => true, 'tech-contact-id' => true, 'billing-contact-id' => true, 'invoice-option' => true, 'protect-privacy' => true);
			$transfer_fields = array_merge(Configure::get("PTisp.transfer_fields"), $domain_field_basics);
			$domain_fields = array_merge(Configure::get("PTisp.domain_fields"), $domain_field_basics);
			$domain_contact_fields = (array)Configure::get("PTisp.contact_fields" . $tld);

			$input_fields = array_merge($contact_fields, $customer_fields, $transfer_fields, $domain_fields, $domain_field_basics, $domain_contact_fields);
		}

		if (isset($vars['use_module']) && $vars['use_module'] == "true") {
			if ($package->meta->type == "domain") {

				$contact_type = $this->getContactType($tld);
				$order_id = null;
				$vars['years'] = 1;

				foreach ($package->pricing as $pricing) {
					if ($pricing->id == $vars['pricing_id']) {
						$vars['years'] = $pricing->term;
						break;
					}
				}

				if (!isset($this->Clients))
					Loader::loadModels($this, array("Clients"));

				$client = $this->Clients->get($vars['client_id']);
				$customer_id = $this->getCustomerId($package->module_row, $client->email);
				$contact_id = null;

				if (!isset($this->Contacts))
						Loader::loadModels($this, array("Contacts"));

				$client = $this->Clients->get($vars['client_id']);
				$numbers = $this->Contacts->getNumbers($client->contact_id, "phone");

				foreach (array_merge($contact_fields, $customer_fields) as $key => $field) {
					if ($key == "name")
						$vars[$key] = $client->first_name . " " . $client->last_name;
					elseif ($key == "company")
						$vars[$key] = $client->company != "" ? $client->company : "Not Applicable";
					elseif ($key == "mail")
						$vars[$key] = $client->email;
					elseif ($key == "vat")
						$vars[$key] = $client->settings["tax_id"];
					elseif ($key == "street")
						$vars[$key] = $client->address1;
					elseif ($key == "city")
						$vars[$key] = $client->city;
					elseif ($key == "state")
						$vars[$key] = $client->state;
					elseif ($key == "postalcode")
						$vars[$key] = $client->zip;
					elseif ($key == "country")
						$vars[$key] = $client->country;
					elseif ($key == "phone") {
							$vars[$key] = $this->formatPhone(isset($numbers[0]) ? $numbers[0]->number : null, $client->country);
					}
				}


				$vars['type'] = $contact_type;
				$vars['customer-id'] = $customer_id;

				$contact_id = $this->createContact($vars['domain-name'], $vars, $username, $password);

				if($contact_id == null)
					return;

				$var["contact"] = $contact_id;
				$var["nichandle"] = $nic;

				// Handle transfer
				if (isset($vars['transfer']) || isset($vars['auth-code'])) {
          // transfer

				}
				// Handle registration
				else {
					// Set nameservers
					$vars['ns'] = array();
					for ($i=1; $i<=4; $i++) {
						if (isset($vars["ns" . $i]) && $vars["ns" . $i] != "")
							$vars['ns'][] = $vars["ns" . $i];
					}

          //REGISTER
					$regperiod = 1;
          $request = new RestRequest("https://api.ptisp.pt/domains/" . $vars['domain-name'] . "/register/" . $regperiod, "POST");
          $request->setUsername($username);
          $request->setPassword($password);
          $request->execute($var);

          $result = json_decode($request->getResponseBody(), true);

          if ($result["result"] != "ok") {
						$this->Input->setRules($this->getDomRules($result));
						$this->Input->validates($result);
          } else {
            // registry ok
						return array(
							array('key' => "domain-name", 'value' => $vars['domain-name'], 'encrypted' => 0),
							array('key' => "order-id", 'value' => $order_id, 'encrypted' => 0)
						);
          }
				}


			}
		} elseif ($status != "pending") {
			if ($package->meta->type == "domain") {
        error_log(print_r("Satus pending", true));
			}
		}

		$meta = array();
		$fields = array_intersect_key($vars, array_merge(array('ns1' => true,'ns2' => true,'ns3' => true,'ns4' => true,'ns5' => true), $input_fields));

		foreach ($fields as $key => $value) {
			$meta[] = array(
				'key' => $key,
				'value' => $value,
				'encrypted' => 0
			);
		}

		return $meta;
	}

	private function getDomRules(&$result) {
		return array(
			'result' => array(
				'valid' => array(
					'rule' => array("compares", "==", "ok"),
					'message' => $result["message"]
				)
			)
		);
	}

	/**
	 * Edits the service on the remote server. Sets Input errors on failure,
	 * preventing the service from being edited.
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $vars An array of user supplied info to satisfy the request
	 * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
	 * @param stdClass $parent_service A stdClass object representing the parent service of the service being edited (if the current service is an addon service)
	 * @return array A numerically indexed array of meta fields to be stored for this service containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function editService($package, $service, array $vars=array(), $parent_package=null, $parent_service=null) {
		return null; // All this handled by admin/client tabs instead
	}

	/**
	 * Cancels the service on the remote server. Sets Input errors on failure,
	 * preventing the service from being canceled.
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
	 * @param stdClass $parent_service A stdClass object representing the parent service of the service being canceled (if the current service is an addon service)
	 * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function cancelService($package, $service, $parent_package=null, $parent_service=null) {
		return null; // Nothing to do
	}

	/**
	 * Suspends the service on the remote server. Sets Input errors on failure,
	 * preventing the service from being suspended.
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
	 * @param stdClass $parent_service A stdClass object representing the parent service of the service being suspended (if the current service is an addon service)
	 * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function suspendService($package, $service, $parent_package=null, $parent_service=null) {
		return null; // Nothing to do
	}

	/**
	 * Unsuspends the service on the remote server. Sets Input errors on failure,
	 * preventing the service from being unsuspended.
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
	 * @param stdClass $parent_service A stdClass object representing the parent service of the service being unsuspended (if the current service is an addon service)
	 * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function unsuspendService($package, $service, $parent_package=null, $parent_service=null) {
		return null; // Nothing to do
	}

	/**
	 * Allows the module to perform an action when the service is ready to renew.
	 * Sets Input errors on failure, preventing the service from renewing.
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
	 * @param stdClass $parent_service A stdClass object representing the parent service of the service being renewed (if the current service is an addon service)
	 * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function renewService($domain, $years, $parent_package=null, $parent_service=null) {

		$row = $this->getModuleRow($package->module_row);
		$username = $row->meta->reseller_id;
		$password = $row->meta->key;

    $request = new RestRequest("https://api.ptisp.pt/domains/" . $domain . "/renew/" . $years, "POST");
    $request->setUsername($username);
    $request->setPassword($password);

    $request->execute();
    $result = json_decode($request->getResponseBody(), true);

    if ($result["result"] != "ok") {
      return false;
    }


    return true;
	}

	/**
	 * Updates the package for the service on the remote server. Sets Input
	 * errors on failure, preventing the service's package from being changed.
	 *
	 * @param stdClass $package_from A stdClass object representing the current package
	 * @param stdClass $package_to A stdClass object representing the new package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
	 * @param stdClass $parent_service A stdClass object representing the parent service of the service being changed (if the current service is an addon service)
	 * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function changeServicePackage($package_from, $package_to, $service, $parent_package=null, $parent_service=null) {
		return null; // Nothing to do
	}

	/**
	 * Validates input data when attempting to add a package, returns the meta
	 * data to save when adding a package. Performs any action required to add
	 * the package on the remote server. Sets Input errors on failure,
	 * preventing the package from being added.
	 *
	 * @param array An array of key/value pairs used to add the package
	 * @return array A numerically indexed array of meta fields to be stored for this package containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function addPackage(array $vars=null) {

		$meta = array();
		if (isset($vars['meta']) && is_array($vars['meta'])) {
			// Return all package meta fields
			foreach ($vars['meta'] as $key => $value) {
				$meta[] = array(
					'key' => $key,
					'value' => $value,
					'encrypted' => 0
				);
			}
		}

		return $meta;
	}

	/**
	 * Validates input data when attempting to edit a package, returns the meta
	 * data to save when editing a package. Performs any action required to edit
	 * the package on the remote server. Sets Input errors on failure,
	 * preventing the package from being edited.
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param array An array of key/value pairs used to edit the package
	 * @return array A numerically indexed array of meta fields to be stored for this package containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function editPackage($package, array $vars=null) {

		$meta = array();
		if (isset($vars['meta']) && is_array($vars['meta'])) {
			// Return all package meta fields
			foreach ($vars['meta'] as $key => $value) {
				$meta[] = array(
					'key' => $key,
					'value' => $value,
					'encrypted' => 0
				);
			}
		}

		return $meta;
	}

	/**
	 * Returns the rendered view of the manage module page
	 *
	 * @param mixed $module A stdClass object representing the module and its rows
	 * @param array $vars An array of post data submitted to or on the manage module page (used to repopulate fields after an error)
	 * @return string HTML content containing information to display when viewing the manager module page
	 */
	public function manageModule($module, array &$vars) {
		// Load the view into this object, so helpers can be automatically added to the view
		$this->view = new View("manage", "default");
		$this->view->base_uri = $this->base_uri;
		$this->view->setDefaultView("components" . DS . "modules" . DS . "ptisp" . DS);

		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html", "Widget"));

		$this->view->set("module", $module);

		return $this->view->fetch();
	}

	/**
	 * Returns the rendered view of the add module row page
	 *
	 * @param array $vars An array of post data submitted to or on the add module row page (used to repopulate fields after an error)
	 * @return string HTML content containing information to display when viewing the add module row page
	 */
	public function manageAddRow(array &$vars) {
		// Load the view into this object, so helpers can be automatically added to the view
		$this->view = new View("add_row", "default");
		$this->view->base_uri = $this->base_uri;
		$this->view->setDefaultView("components" . DS . "modules" . DS . "ptisp" . DS);

		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html", "Widget"));

		// Set unspecified checkboxes
		if (!empty($vars)) {
			if (empty($vars['sandbox']))
				$vars['sandbox'] = "false";
		}

		$this->view->set("vars", (object)$vars);
		return $this->view->fetch();
	}

	/**
	 * Returns the rendered view of the edit module row page
	 *
	 * @param stdClass $module_row The stdClass representation of the existing module row
	 * @param array $vars An array of post data submitted to or on the edit module row page (used to repopulate fields after an error)
	 * @return string HTML content containing information to display when viewing the edit module row page
	 */
	public function manageEditRow($module_row, array &$vars) {
		// Load the view into this object, so helpers can be automatically added to the view
		$this->view = new View("edit_row", "default");
		$this->view->base_uri = $this->base_uri;
		$this->view->setDefaultView("components" . DS . "modules" . DS . "ptisp" . DS);

		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html", "Widget"));

		if (empty($vars))
			$vars = $module_row->meta;
		else {
			// Set unspecified checkboxes
			if (empty($vars['sandbox']))
				$vars['sandbox'] = "false";
		}

		$this->view->set("vars", (object)$vars);
		return $this->view->fetch();
	}

	/**
	 * Adds the module row on the remote server. Sets Input errors on failure,
	 * preventing the row from being added.
	 *
	 * @param array $vars An array of module info to add
	 * @return array A numerically indexed array of meta fields for the module row containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 */
	public function addModuleRow(array &$vars) {
		$meta_fields = array("registrar", "reseller_id", "key", "epp", "sandbox");
		$encrypted_fields = array("key");

		// Set unspecified checkboxes
		if (empty($vars['sandbox']))
			$vars['sandbox'] = "false";

    $vars["registrar"]= "Ptisp";
    $vars["sandbox"]= "false";
		$this->Input->setRules($this->getRowRules($vars));

		// Validate module row
		if ($this->Input->validates($vars)) {

			// Build the meta data for this row
			$meta = array();
			foreach ($vars as $key => $value) {

				if (in_array($key, $meta_fields)) {
					$meta[] = array(
						'key' => $key,
						'value' => $value,
						'encrypted' => in_array($key, $encrypted_fields) ? 1 : 0
					);
				}
			}

			return $meta;
		}
	}

	/**
	 * Edits the module row on the remote server. Sets Input errors on failure,
	 * preventing the row from being updated.
	 *
	 * @param stdClass $module_row The stdClass representation of the existing module row
	 * @param array $vars An array of module info to update
	 * @return array A numerically indexed array of meta fields for the module row containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 */
	public function editModuleRow($module_row, array &$vars) {
		// Same as adding
		return $this->addModuleRow($vars);
	}

	/**
	 * Deletes the module row on the remote server. Sets Input errors on failure,
	 * preventing the row from being deleted.
	 *
	 * @param stdClass $module_row The stdClass representation of the existing module row
	 */
	public function deleteModuleRow($module_row) {

	}

	/**
	 * Returns all fields used when adding/editing a package, including any
	 * javascript to execute when the page is rendered with these fields.
	 *
	 * @param $vars stdClass A stdClass object representing a set of post fields
	 * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
	 */
	public function getPackageFields($vars=null) {
		Loader::loadHelpers($this, array("Html"));

		$fields = new ModuleFields();

		$types = array(
			'domain' => Language::_("PTisp.package_fields.type_domain", true),
		);

		// Set type of package
		$type = $fields->label(Language::_("PTisp.package_fields.type", true), "ptisp_type");
		$type->attach($fields->fieldSelect("meta[type]", $types,
			$this->Html->ifSet($vars->meta['type']), array('id'=>"ptisp_type")));
		$fields->setField($type);

		// Set all TLD checkboxes
        $tld_options = $fields->label(Language::_("PTisp.package_fields.tld_options", true));

		$tlds = Configure::get("PTisp.tlds");
		sort($tlds);
		foreach ($tlds as $tld) {
			$tld_label = $fields->label($tld, "tld_" . $tld);
			$tld_options->attach($fields->fieldCheckbox("meta[tlds][]", $tld, (isset($vars->meta['tlds']) && in_array($tld, $vars->meta['tlds'])), array('id' => "tld_" . $tld), $tld_label));
		}
		$fields->setField($tld_options);

		// Set nameservers
		for ($i=1; $i<=4; $i++) {
			$type = $fields->label(Language::_("PTisp.package_fields.ns" . $i, true), "ptisp_ns" . $i);
			$type->attach($fields->fieldText("meta[ns][]",
				$this->Html->ifSet($vars->meta['ns'][$i-1]), array('id'=>"ptisp_ns" . $i)));
			$fields->setField($type);
		}

		$fields->setHtml("
			<script type=\"text/javascript\">
				$(document).ready(function() {
					toggleTldOptions($('#ptisp_type').val());

					// Re-fetch module options to toggle fields
					$('#ptisp_type').change(function() {
						toggleTldOptions($(this).val());
					});

					function toggleTldOptions(type) {
						if (type == 'ssl')
							$('.ptisp_tlds').hide();
						else
							$('.ptisp_tlds').show();
					}
				});
			</script>
		");

		return $fields;
	}

	/**
	 * Returns an array of key values for fields stored for a module, package,
	 * and service under this module, used to substitute those keys with their
	 * actual module, package, or service meta values in related emails.
	 *
	 * @return array A multi-dimensional array of key/value pairs where each key is one of 'module', 'package', or 'service' and each value is a numerically indexed array of key values that match meta fields under that category.
	 * @see Modules::addModuleRow()
	 * @see Modules::editModuleRow()
	 * @see Modules::addPackage()
	 * @see Modules::editPackage()
	 * @see Modules::addService()
	 * @see Modules::editService()
	 */
	public function getEmailTags() {
		return array('service' => array('domain-name'));
	}

	/**
	 * Returns all fields to display to an admin attempting to add a service with the module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param $vars stdClass A stdClass object representing a set of post fields
	 * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
	 */
	public function getAdminAddFields($package, $vars=null) {

		// Handle universal domain name
		if (isset($vars->domain))
			$vars->{'domain-name'} = $vars->domain;

		if ($package->meta->type == "domain") {
			// Set default name servers
			if (!isset($vars->ns1) && isset($package->meta->ns)) {
				$i=1;
				foreach ($package->meta->ns as $ns) {
					$vars->{"ns" . $i++} = $ns;
				}
			}

			// Handle transfer request
			if (isset($vars->transfer) || isset($vars->{'auth-code'})) {
				return $this->arrayToModuleFields(Configure::get("PTisp.transfer_fields"), null, $vars);
			}
			// Handle domain registration
			else {

				$module_fields = $this->arrayToModuleFields(array_merge(Configure::get("PTisp.domain_fields"), Configure::get("PTisp.nameserver_fields")), null, $vars);

				if (isset($vars->{'domain-name'})) {
					$tld = $this->getTld($vars->{'domain-name'});

					if ($tld) {
						$extension_fields = array_merge((array)Configure::get("PTisp.domain_fields" . $tld), (array)Configure::get("PTisp.contact_fields" . $tld));
						if ($extension_fields)
							$module_fields = $this->arrayToModuleFields($extension_fields, $module_fields, $vars);
					}
				}

				return $module_fields;
			}
		}
		else {
			return new ModuleFields();
		}
	}

	/**
	 * Returns all fields to display to a client attempting to add a service with the module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param $vars stdClass A stdClass object representing a set of post fields
	 * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
	 */
	public function getClientAddFields($package, $vars=null) {

		// Handle universal domain name
		if (isset($vars->domain))
			$vars->{'domain-name'} = $vars->domain;

		if ($package->meta->type == "domain") {

			// Set default name servers
			if (!isset($vars->ns1) && isset($package->meta->ns)) {
				$i=1;
				foreach ($package->meta->ns as $ns) {
					$vars->{"ns" . $i++} = $ns;
				}
			}

			$tld = (property_exists($vars, "domain-name") ? $this->getTld($vars->{'domain-name'}, true) : null);

			// Handle transfer request
			if (isset($vars->transfer) || isset($vars->{'auth-code'})) {
				$fields = Configure::get("PTisp.transfer_fields");

				// We should already have the domain name don't make editable
				$fields['domain-name']['type'] = "hidden";
				$fields['domain-name']['label'] = null;

				$module_fields = $this->arrayToModuleFields($fields, null, $vars);

				$extension_fields = Configure::get("PTisp.contact_fields" . $tld);
				if ($extension_fields)
					$module_fields = $this->arrayToModuleFields($extension_fields, $module_fields, $vars);

				return $module_fields;
			}
			// Handle domain registration
			else {
				$fields = array_merge(Configure::get("PTisp.nameserver_fields"), Configure::get("PTisp.domain_fields"));

				// We should already have the domain name don't make editable
				$fields['domain-name']['type'] = "hidden";
				$fields['domain-name']['label'] = null;

				$module_fields = $this->arrayToModuleFields($fields, null, $vars);

				if (isset($vars->{'domain-name'})) {
					$extension_fields = array_merge((array)Configure::get("PTisp.domain_fields" . $tld), (array)Configure::get("PTisp.contact_fields" . $tld));
					if ($extension_fields)
						$module_fields = $this->arrayToModuleFields($extension_fields, $module_fields, $vars);
				}

				return $module_fields;
			}
		}
		else {
			return new ModuleFields();
		}
	}

	/**
	 * Returns all fields to display to an admin attempting to edit a service with the module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param $vars stdClass A stdClass object representing a set of post fields
	 * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
	 */
	public function getAdminEditFields($package, $vars=null) {
		if ($package->meta->type == "domain") {
			return new ModuleFields();
		}
		else {
			return new ModuleFields();
		}
	}

	/**
	 * Fetches the HTML content to display when viewing the service info in the
	 * admin interface.
	 *
	 * @param stdClass $service A stdClass object representing the service
	 * @param stdClass $package A stdClass object representing the service's package
	 * @return string HTML content containing information to display when viewing the service info
	 */
	public function getAdminServiceInfo($service, $package) {
		return "";
	}

	/**
	 * Fetches the HTML content to display when viewing the service info in the
	 * client interface.
	 *
	 * @param stdClass $service A stdClass object representing the service
	 * @param stdClass $package A stdClass object representing the service's package
	 * @return string HTML content containing information to display when viewing the service info
	 */
	public function getClientServiceInfo($service, $package) {
		return "";
	}

	/**
	 * Returns all tabs to display to an admin when managing a service whose
	 * package uses this module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @return array An array of tabs in the format of method => title. Example: array('methodName' => "Title", 'methodName2' => "Title2")
	 */
	public function getAdminTabs($package) {
		if ($package->meta->type == "domain") {
			return array(
				'tabNameservers' => Language::_("PTisp.tab_nameservers.title", true),
				'tabSettings' => Language::_("PTisp.tab_settings.title", true)
			);
		}
	}

	/**
	 * Returns all tabs to display to a client when managing a service whose
	 * package uses this module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @return array An array of tabs in the format of method => title. Example: array('methodName' => "Title", 'methodName2' => "Title2")
	 */
	public function getClientTabs($package) {
		if ($package->meta->type == "domain") {
			return array(
				'tabClientNameservers' => Language::_("PTisp.tab_nameservers.title", true),
				'tabClientSettings' => Language::_("PTisp.tab_settings.title", true)
			);
		}
	}

	/**
	 * Admin Whois tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabWhois($package, $service, array $get=null, array $post=null, array $files=null) {
		return null;
	}

	/**
	 * Client Whois tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabClientWhois($package, $service, array $get=null, array $post=null, array $files=null) {
		return null;
	}

	/**
	 * Admin Nameservers tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabNameservers($package, $service, array $get=null, array $post=null, array $files=null) 	{

		return $this->manageNameservers("tab_nameservers", $package, $service, $get, $post, $files);
	}

	/**
	 * Admin Nameservers tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabClientNameservers($package, $service, array $get=null, array $post=null, array $files=null) {

		return $this->manageNameservers("tab_client_nameservers", $package, $service, $get, $post, $files);
	}

	/**
	 * Admin Settings tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabSettings($package, $service, array $get=null, array $post=null, array $files=null) {
		return $this->manageSettings("tab_settings", $package, $service, $get, $post, $files);
	}

	/**
	 * Client Settings tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabClientSettings($package, $service, array $get=null, array $post=null, array $files=null) {
		return $this->manageSettings("tab_client_settings", $package, $service, $get, $post, $files);
	}

	/**
	 * Handle updating nameserver information
	 *
	 * @param string $view The view to use
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	private function manageNameservers($view, $package, $service, array $get=null, array $post=null, array $files=null) {
		$vars = new stdClass();

		$row = $this->getModuleRow($package->module_row);
		$username = $row->meta->reseller_id;
		$password = $row->meta->key;

		$fields = $this->serviceFieldsToObject($service->fields);
		$domain = $fields->{'domain-name'};


		//Post change NS
		if (isset($post["ns"][0]) && ($post["ns"][0] != "")) {
			$ns = $post["ns"][0];
			if (isset($post["ns"][1]) && ($post["ns"][1] != "")){
					$ns .= "/" . $post["ns"][1];
			}
			if (isset($post["ns"][2]) && ($post["ns"][2] != "")){
					$ns .= "/" . $post["ns"][2];
			}
			if (isset($post["ns"][3]) && ($post["ns"][3] != "")){
					$ns .= "/" . $post["ns"][3];
			}
			$request = new RestRequest("https://api.ptisp.pt/domains/" . $domain . "/update/ns/" .$ns, "POST");
			$request->setUsername($username);
			$request->setPassword($password);
			$request->execute(array());
		}

    $request = new RestRequest("https://api.ptisp.pt/domains/" . $domain . "/info", "GET");
    $request->setUsername($username);
    $request->setPassword($password);
    $request->execute();
    $show_content = true;

    $result = json_decode($request->getResponseBody(), true);

    if ($result["result"] != "ok") {
      $vars->ns[] = array();
      $show_content = false;
    } else {
				if (isset($result["data"]["ns"][0])) {
					$vars->ns[0] = $result["data"]["ns"][0];
				}
				if (isset($result["data"]["ns"][1])) {
					$vars->ns[1] = $result["data"]["ns"][1];
				}
				if (isset($result["data"]["ns"][2])) {
					$vars->ns[2] = $result["data"]["ns"][2];
				}
				if (isset($result["data"]["ns"][3])) {
					$vars->ns[3] = $result["data"]["ns"][3];
				}
    }


		$view = ($show_content ? $view : "tab_unavailable");
		$this->view = new View($view, "default");

		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html"));

		$this->view->set("vars", $vars);
		$this->view->setDefaultView("components" . DS . "modules" . DS . "ptisp" . DS);
		return $this->view->fetch();
	}

	/**
	 * Handle updating settings
	 *
	 * @param string $view The view to use
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	private function manageSettings($view, $package, $service, array $get=null, array $post=null, array $files=null) {
		$vars = new stdClass();

		$row = $this->getModuleRow($package->module_row);


		$fields = $this->serviceFieldsToObject($service->fields);
		$show_content = true;

		if (property_exists($fields, "order-id")) {
			if (!empty($post)) {

				if (isset($post['registrar_lock'])) {
					if ($post['registrar_lock'] == "true") {
						//DOMAIN LOCK
					}
				}

				$vars = (object)$post;
			}
		}
		else {
			// No order-id; info is not available
			$show_content = false;
		}

		$view = ($show_content ? $view : "tab_unavailable");
		$this->view = new View($view, "default");

		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html"));

		$this->view->set("vars", $vars);
		$this->view->setDefaultView("components" . DS . "modules" . DS . "ptisp" . DS);
		return $this->view->fetch();
	}

	/**
	 * Creates a contact
	 *
	 * @param int $module_row_id The module row ID to add the contact under
	 * @param array $vars An array of contact information
	 * @return int The contact-id created, null otherwise
	 */
	private function createContact($domain, $vars, $username, $password) {
		$row = $this->getModuleRow($module_row_id);
		$username = $row->meta->reseller_id;
		$password = $row->meta->key;

		$request = new RestRequest("https://api.ptisp.pt/domains/" . $domain . "/contacts/create", "POST");
    $request->setUsername($username);
    $request->setPassword($password);

    $request->execute($vars);
    $result = json_decode($request->getResponseBody(), true);

		if ($result["result"] == "ok" )
			return $result["nichandle"];
		else{
			$this->Input->setRules($this->getDomRules($result));
			$this->Input->validates($result);

			return null;
		}
	}

	/**
	 * Fetches the PTisp customer ID based on username
	 *
	 * @param int $module_row_id The module row ID to search on
	 * @param string $username The customer username (should be an email address)
	 * @return int The PTisp customer-id if one exists, null otherwise
	 */
	private function getCustomerId($module_row_id, $username) {
		$row = $this->getModuleRow($module_row_id);
		$username = $row->meta->reseller_id;
		$password = $row->meta->key;

		$request = new RestRequest("https://api.ptisp.pt/user/info", "GET");
		$request->setUsername($username);
		$request->setPassword($password);
		$request->execute();
		$show_content = true;

		$result = json_decode($request->getResponseBody(), true);

		if ($result["result"] != "ok") {
			return 1;
		}
		return null;

	}

	/**
	 * Fetches a contact ID of a given customer ID
	 *
	 * @param int $module_row_id The module row ID to search on
	 * @param string $customer_id The customer-id
	 * @param string $type includes one of:
	 * 	- Contact
	 * 	- CoopContact
	 * 	- UkContact
	 * 	- EuContact
	 * 	- Sponsor
	 * 	- CnContact
	 * 	- CoContact
	 * 	- CaContact
	 * 	- DeContact
	 * 	- EsContact
	 * @return
	 */
	private function getContactId($module_row_id, $customer_id, $type="Contact") {

		return null;
	}

	/**
	 * Return the contact type required for the given TLD
	 *
	 * @param $tld The TLD to return the contact type for
	 * @return string The contact type
	 */
	private function getContactType($tld) {
		$type = "Contact";
		// Detect contact type from TLD
		if (($tld_part = ltrim(strstr($tld, "."), ".")) &&
			in_array($tld_part, array("ca", "cn", "co", "coop", "de", "es", "eu", "nl", "ru", "uk"))) {
			$type = ucfirst($tld_part) . $type;
		}
		return $type;
	}

	/**
	 *
	 * @param $attr array An array of key/value pairs
	 * @retrun array An array of key/value pairs where each $attr[$key] becomes "attr-nameN" and "attr-valueN" whose values are $key and $attr[$key], respectively
	 */
	private function createMap($attr) {
		$map = array();

		$i=1;
		foreach ($attr as $key => $value) {
			if (substr($key, 0, 5) == "attr_") {
				$map['attr-name' . $i] = str_replace("attr_", "", $key);
				$map['attr-value' . $i] = $value;
				$i++;
			}
		}
		return $map;
	}

	/**
	 *
	 * @param string $domain The domain to lookup
	 * @return boolean True if available, false otherwise
	 */
	public function checkAvailability($domain) {
		$row = $this->getModuleRow($package->module_row);
		$username = $row->meta->reseller_id;
		$password = $row->meta->key;

    $request = new RestRequest("https://api.ptisp.pt/domains/" . $domain . "/check", "GET");
    $request->setUsername($username);
    $request->setPassword($password);
    $request->execute();

    $result = json_decode($request->getResponseBody(), true);

    if ($result["result"] != "ok" || $result["available"] != "true") {
      return false;
    }

    return true;
	}

	/**
	 * Builds and returns the rules required to add/edit a module row
	 *
	 * @param array $vars An array of key/value data pairs
	 * @return array An array of Input rules suitable for Input::setRules()
	 */
	private function getRowRules(&$vars) {
		return array(
			'reseller_id' => array(
				'valid' => array(
					'rule' => "isEmpty",
					'negate' => true,
					'message' => Language::_("PTisp.!error.reseller_id.valid", true)
				)
			),
			'key' => array(
				'valid' => array(
					'last' => true,
					'rule' => "isEmpty",
					'negate' => true,
					'message' => Language::_("PTisp.!error.key.valid", true)
				),
				'valid_connection' => array(
					'rule' => array(array($this, "validateConnection"), $vars['reseller_id'], isset($vars['sandbox']) ? $vars['sandbox'] : "false"),
					'message' => Language::_("PTisp.!error.key.valid_connection", true)
				)
			),
			'epp' => array(
				'valid' => array(
					'rule' => "isEmpty",
					'negate' => true,
					'message' => Language::_("PTisp.!error.epp.valid", true)
				)
			)
		);
	}

	/**
	 * Validates that the given connection details are correct by attempting to check the availability of a domain
	 *
	 * @param string $key The API key
	 * @param string $reseller_id The API reseller ID
	 * @param string $sandbox "true" if this is a sandbox account, false otherwise
	 * @return boolean True if the connection details are valid, false otherwise
	 */
	public function validateConnection($key, $reseller_id, $sandbox) {

		return true;
	}

	/**
	 * @param string $reseller_id The reseller ID to connect as
	 * @param string $key The key to use when connecting
	 * @param boolean $sandbox Whether or not to process in sandbox mode (for testing)
	 */
	private function getApi($reseller_id, $key, $sandbox) {
		Loader::load(dirname(__FILE__) . DS . "apis" . DS . "RestRequest.inc.php");

		return null;
	}


	/**
	 * Returns the TLD of the given domain
	 *
	 * @param string $domain The domain to return the TLD from
	 * @param boolean $top If true will return only the top TLD, else will return the first matched TLD from the list of TLDs
	 * @return string The TLD of the domain
	 */
	private function getTld($domain, $top = false) {
		$tlds = Configure::get("PTisp.tlds");

		$domain = strtolower($domain);

		if (!$top) {
			foreach ($tlds as $tld) {
				if (substr($domain, -strlen($tld)) == $tld)
					return $tld;
			}
		}
		return strrchr($domain, ".");
	}

	/**
	 * Formats a phone number into +NNN.NNNNNNNNNN
	 *
	 * @param string $number The phone number
	 * @param string $country The ISO 3166-1 alpha2 country code
	 * @return string The number in +NNN.NNNNNNNNNN
	 */
	private function formatPhone($number, $country) {
		if (!isset($this->Contacts))
			Loader::loadModels($this, array("Contacts"));

		return $this->Contacts->intlNumber($number, $country, ".");
	}

	/**
	 * Formats the contact ID for the given TLD and type
	 *
	 * @param int $contact_id The contact ID
	 * @param string $tld The TLD being registered/transferred
	 * @param string $type The contact type
	 * @return int The contact ID to use
	 */
	private function formatContact($contact_id, $tld, $type) {
		$tlds = array();
		switch ($type) {
			case "admin":
			case "tech":
				$tlds = array(".eu", ".nz", ".ru", ".uk");
				break;
			case "billing":
				$tlds = array(".ca", ".eu", ".nl", ".nz", ".ru", ".uk");
				break;
		}
		if (in_array(strtolower($tld), $tlds))
			return -1;
		return $contact_id;
	}
}
?>
