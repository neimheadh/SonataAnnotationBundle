<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Annotation;

/**
 * Datagrid association field annotation.
 *
 * Allow you to configure the datagrid for the annotated field having
 * an association field.
 *
 * @Annotation
 * @Target("PROPERTY")
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class DatagridAssociationField extends DatagridField implements
    AssociationFieldInterface
{

    /**
     * Association field name.
     *
     * @var string
     */
    public string $field;

}
