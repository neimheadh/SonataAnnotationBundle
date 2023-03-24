<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Reader;

use Neimheadh\SonataAnnotationBundle\Annotation\Sonata\DatagridValues;
use ReflectionClass;

/**
 * DatagridValues annotation reader.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class DatagridValuesReader extends AbstractReader
{

    /**
     * Get the list of datagrid values.
     *
     * @param ReflectionClass $class
     *
     * @return array
     */
    public function getDatagridValues(ReflectionClass $class): array
    {
        /** @var DatagridValues|null $annotation */
        if ($annotation = $this->getClassAnnotation(
            $class,
            DatagridValues::class
        )) {
            return $annotation->values;
        }

        return [];
    }

}
