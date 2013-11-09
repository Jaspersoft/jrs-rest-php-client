<?php
namespace Jaspersoft\Dto\Resource;


class SemanticLayerDataSource extends CompositeResource
{
    // NOTE: a schema reference for this resource should be denoted by the key
    // "schemaFileReference" and not the usual "schemaReference"
    public $schema;
    public $securityFile;
    // NOTE: bundles field is composite, but handled in a special manner. It does not
    // appear that bundles can hold a reference, but only an array of other objects
    // containing "locale" and "file" fields.
    public $bundles;
    public $dataSource;
}