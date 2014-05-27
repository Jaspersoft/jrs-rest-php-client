<?php
namespace Jaspersoft\Dto\Attribute;

/** Jasper\Attribute class
 * this class represents Attributes from the JasperServer and contains data that is
 * accessible via the user service in the REST API.
 *
 * author: gbacon
 * date: 06/13/2012
 */
class Attribute  {

    public $name;
    public $value;

    /**
     * Constructor
     *
     * To use this function provide an array in the format array('attributeName' => 'attributeValue').
     * @param string $name - name of attribute
     * @param string $value - value of attribute
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

}

?>
