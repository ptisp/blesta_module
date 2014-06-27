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

// .COOP
Configure::set("PTisp.contact_fields.coop", array(
	'attr_sponsor' => array(
		'label' => Language::_("PTisp.contact.sponsor", true),
		'type' => "text"
	)
));

// .ES
Configure::set("PTisp.contact_fields.es", array(
	'attr_es_form_juridica' => array(
		'type' => "hidden",
		'options' => "1"
		/*
		'label' => Language::_("PTisp.contact.es_form_juridica", true),
		'type' => "select",
		'options' => array(
			'1' => Language::_("PTisp.contact.es_form_juridica.1", true),
			'39' => Language::_("PTisp.contact.es_form_juridica.39", true),
			'47' => Language::_("PTisp.contact.es_form_juridica.47", true),
			'59' => Language::_("PTisp.contact.es_form_juridica.59", true),
			'68' => Language::_("PTisp.contact.es_form_juridica.68", true),
			'124' => Language::_("PTisp.contact.es_form_juridica.124", true),
			'150' => Language::_("PTisp.contact.es_form_juridica.150", true),
			'152' => Language::_("PTisp.contact.es_form_juridica.152", true),
			'164' => Language::_("PTisp.contact.es_form_juridica.164", true),
			'181' => Language::_("PTisp.contact.es_form_juridica.181", true),
			'197' => Language::_("PTisp.contact.es_form_juridica.197", true),
			'203' => Language::_("PTisp.contact.es_form_juridica.203", true),
			'229' => Language::_("PTisp.contact.es_form_juridica.229", true),
			'269' => Language::_("PTisp.contact.es_form_juridica.269", true),
			'286' => Language::_("PTisp.contact.es_form_juridica.286", true),
			'365' => Language::_("PTisp.contact.es_form_juridica.365", true),
			'434' => Language::_("PTisp.contact.es_form_juridica.434", true),
			'436' => Language::_("PTisp.contact.es_form_juridica.436", true),
			'439' => Language::_("PTisp.contact.es_form_juridica.439", true),
			'476' => Language::_("PTisp.contact.es_form_juridica.476", true),
			'510' => Language::_("PTisp.contact.es_form_juridica.510", true),
			'524' => Language::_("PTisp.contact.es_form_juridica.524", true),
			'525' => Language::_("PTisp.contact.es_form_juridica.525", true),
			'554' => Language::_("PTisp.contact.es_form_juridica.554", true),
			'560' => Language::_("PTisp.contact.es_form_juridica.560", true),
			'562' => Language::_("PTisp.contact.es_form_juridica.562", true),
			'566' => Language::_("PTisp.contact.es_form_juridica.566", true),
			'608' => Language::_("PTisp.contact.es_form_juridica.608", true),
			'612' => Language::_("PTisp.contact.es_form_juridica.612", true),
			'713' => Language::_("PTisp.contact.es_form_juridica.713", true),
			'717' => Language::_("PTisp.contact.es_form_juridica.717", true),
			'744' => Language::_("PTisp.contact.es_form_juridica.744", true),
			'745' => Language::_("PTisp.contact.es_form_juridica.745", true),
			'746' => Language::_("PTisp.contact.es_form_juridica.746", true),
			'747' => Language::_("PTisp.contact.es_form_juridica.747", true),
			'877' => Language::_("PTisp.contact.es_form_juridica.877", true),
			'878' => Language::_("PTisp.contact.es_form_juridica.878", true),
			'879' => Language::_("PTisp.contact.es_form_juridica.879", true)
		)
		*/
	),
	'attr_es_tipo_identificacion' => array(
		'label' => Language::_("PTisp.contact.es_tipo_identificacion", true),
		'type' => "select",
		'options' => array(
			'1' => Language::_("PTisp.contact.es_tipo_identificacion.1", true),
			'3' => Language::_("PTisp.contact.es_tipo_identificacion.3", true),
			'0' => Language::_("PTisp.contact.es_tipo_identificacion.0", true)
		)
	),
	'attr_es_identificacion' => array(
		'label' => Language::_("PTisp.contact.es_identificacion", true),
		'type' => "text"
	)
));

// .NL
Configure::set("PTisp.contact_fields.nl", array(
	'attr_legalForm' => array(
		'label' => Language::_("PTisp.contact.legalForm", true),
		'type' => "select",
		'options' => array(
			'PERSOON' => Language::_("PTisp.contact.legalForm.persoon", true),
			'ANDERS' => Language::_("PTisp.contact.legalForm.anders", true)
		)
	)
));

// .PRO
Configure::set("PTisp.contact_fields.pro", array(
	'attr_profession' => array(
		'label' => Language::_("PTisp.contact.profession", true),
		'type' => "text"
	)
));

// .RU
Configure::set("PTisp.contact_fields.ru", array(
	'attr_contract-type' => array(
		'label' => Language::_("PTisp.contact.contract-type", true),
		'type' => "select",
		'options' => array(
			'ORG' => Language::_("PTisp.contact.contract-type.org", true),
			'PRS' => Language::_("PTisp.contact.contract-type.prs", true)
		)
	),
	'attr_birth-date' => array(
		'label' => Language::_("PTisp.contact.birth-date", true),
		'type' => "text"
	),
	/*
	'attr_org-r' => array(
		'label' => Language::_("PTisp.contact.org-r", true),
		'type' => "text"
	),
	'attr_person-r' => array(
		'label' => Language::_("PTisp.contact.person-r", true),
		'type' => "text"
	),
	'attr_address-r' => array(
		'label' => Language::_("PTisp.contact.address-r", true),
		'type' => "text"
	),
	*/
	'attr_kpp' => array(
		'label' => Language::_("PTisp.contact.kpp", true),
		'type' => "text"
	),
	'attr_code' => array(
		'label' => Language::_("PTisp.contact.code", true),
		'type' => "text"
	),
	'attr_passport' => array(
		'label' => Language::_("PTisp.contact.passport", true),
		'type' => "text"
	),
));

// .US
Configure::set("PTisp.contact_fields.us", array(
	'attr_category' => array(
		'label' => Language::_("PTisp.contact.category", true),
		'type' => "select",
		'options' => array(
			'C11' => Language::_("PTisp.contact.category.c11", true),
			'C12' => Language::_("PTisp.contact.category.c12", true),
			'C21' => Language::_("PTisp.contact.category.c21", true),
			'C31' => Language::_("PTisp.contact.category.c31", true),
			'C32' => Language::_("PTisp.contact.category.c32", true)
		)
	),
	'attr_purpose' => array(
		'label' => Language::_("PTisp.contact.purpose", true),
		'type' => "select",
		'options' => array(
			'P1' => Language::_("PTisp.contact.purpose.p1", true),
			'P2' => Language::_("PTisp.contact.purpose.p2", true),
			'P3' => Language::_("PTisp.contact.purpose.p3", true),
			'P4' => Language::_("PTisp.contact.purpose.p4", true),
			'P5' => Language::_("PTisp.contact.purpose.p5", true)
		)
	)
));


// .AU
Configure::set("PTisp.domain_fields.au", array(
	'attr_id-type' => array(
		'label' => Language::_("PTisp.domain.id-type", true),
		'type' => "select",
		'options' => array(
			'ACN' => Language::_("PTisp.domain.id-type.acn", true),
			'ABN' => Language::_("PTisp.domain.id-type.abn", true),
			'VIC BN' => Language::_("PTisp.domain.id-type.vic_bn", true),
			'NSW BN' => Language::_("PTisp.domain.id-type.nsw_bn", true),
			'SA BN' => Language::_("PTisp.domain.id-type.sa_bn", true),
			'NT BN' => Language::_("PTisp.domain.id-type.nt_bn", true),
			'WA BN' => Language::_("PTisp.domain.id-type.wa_bn", true),
			'TAS BN' => Language::_("PTisp.domain.id-type.tas_bn", true),
			'ACT BN' => Language::_("PTisp.domain.id-type.act_bn", true),
			'QLD BN' => Language::_("PTisp.domain.id-type.qld_bn", true),
			'TM' => Language::_("PTisp.domain.id-type.tm", true),
			'ARBN' => Language::_("PTisp.domain.id-type.arbn", true),
			'Other' => Language::_("PTisp.domain.id-type.other", true)
		)
	),
	'attr_id' => array(
		'label' => Language::_("PTisp.domain.id", true),
		'type' => "text"
	),
	'attr_policyReason' => array(
		'label' => Language::_("PTisp.domain.policyReason", true),
		'type' => "radio",
		'value' => "1",
		'options' => array(
			'1' => Language::_("PTisp.domain.policyReason.1", true),
			'2' => Language::_("PTisp.domain.policyReason.2", true),
		)
	),
	'attr_isAUWarranty' => array(
		'label' => Language::_("PTisp.domain.isAUWarranty", true),
		'type' => "checkbox",
		'options' => array(
			'true' => Language::_("PTisp.domain.isAUWarranty.true", true)
		)
	),
	'attr_eligibilityType' => array(
		'type' => "hidden",
		'options' => "Trademark Owner"
	),
	'attr_eligibilityName' => array(
		'type' => "hidden"
	)
));
?>
