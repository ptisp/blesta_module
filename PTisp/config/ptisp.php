<?php
//
Configure::set("PTisp.tlds", array(
	".com",
	".net",
	".org",
	".eu",
  ".pt",
  ".com.pt",
  ".org.pt"
));

// Transfer fields
Configure::set("PTisp.transfer_fields", array(
	'domain-name' => array(
		'label' => Language::_("PTisp.transfer.domain-name", true),
		'type' => "text"
	),
	'auth-code' => array(
		'label' => Language::_("PTisp.transfer.auth-code", true),
		'type' => "text"
	)
));

// Domain fields
Configure::set("PTisp.domain_fields", array(
	'domain-name' => array(
		'label' => Language::_("PTisp.domain.domain-name", true),
		'type' => "text"
	)
));

// Nameserver fields
Configure::set("PTisp.nameserver_fields", array(
	'ns1' => array(
		'label' => Language::_("PTisp.nameserver.ns1", true),
		'type' => "text"
	),
	'ns2' => array(
		'label' => Language::_("PTisp.nameserver.ns2", true),
		'type' => "text"
	),
	'ns3' => array(
		'label' => Language::_("PTisp.nameserver.ns3", true),
		'type' => "text"
	),
	'ns4' => array(
		'label' => Language::_("PTisp.nameserver.ns4", true),
		'type' => "text"
	)
));

// Contact fields
Configure::set("PTisp.contact_fields", array(
	'customer-id' => array(
		'label' => Language::_("PTisp.contact.customer-id", true),
		'type' => "text"
	),
	'type' => array(
		'label' => Language::_("PTisp.contact.type", true),
		'type' => "text"
	),
	'name' => array(
		'label' => Language::_("PTisp.contact.name", true),
		'type' => "text"
	),
	'company' => array(
		'label' => Language::_("PTisp.contact.company", true),
		'type' => "text"
	),
	'mail' => array(
		'label' => Language::_("PTisp.contact.mail", true),
		'type' => "text"
	),
	'vat' => array(
		'label' => Language::_("PTisp.contact.vat", true),
		'type' => "text"
	),
	'street' => array(
		'label' => Language::_("PTisp.contact.street", true),
		'type' => "text"
	),
	'address-line-2' => array(
		'label' => Language::_("PTisp.contact.address-line-2", true),
		'type' => "text"
	),
	'city' => array(
		'label' => Language::_("PTisp.contact.city", true),
		'type' => "text"
	),
	'state' => array(
		'label' => Language::_("PTisp.contact.state", true),
		'type' => "text"
	),
	'postalcode' => array(
		'label' => Language::_("PTisp.contact.postalcode", true),
		'type' => "text"
	),
	'country' => array(
		'label' => Language::_("PTisp.contact.country", true),
		'type' => "text"
	),
	'phone-cc' => array(
		'label' => Language::_("PTisp.contact.phone-cc", true),
		'type' => "text"
	),
	'phone' => array(
		'label' => Language::_("PTisp.contact.phone", true),
		'type' => "text"
	),
	'fax-cc' => array(
		'label' => Language::_("PTisp.contact.fax-cc", true),
		'type' => "text"
	),
	'fax' => array(
		'label' => Language::_("PTisp.contact.fax", true),
		'type' => "text"
	)
));

// Customer info
Configure::set("PTisp.customer_fields", array(
	'username' => array(
		'label' => Language::_("PTisp.customer.username", true),
		'type' => "text"
	),
	'passwd' => array(
		'label' => Language::_("PTisp.customer.passwd", true),
		'type' => "text"
	),
	'lang-pref' => array(
		'label' => Language::_("PTisp.customer.lang-pref", true),
		'type' => "text"
	)
));

?>
