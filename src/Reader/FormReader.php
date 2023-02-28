<?php

declare(strict_types=1);

namespace Neimheadh\SonataAnnotationBundle\Reader;

use Doctrine\Common\Annotations\Reader;
use Neimheadh\SonataAnnotationBundle\Annotation\FormField;
use ReflectionClass;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Form configuration reader.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class FormReader extends AbstractFieldConfigurationReader
{

    /**
     * {@inheritDoc}
     */
    public function __construct(
        Reader $annotationReader
    ) {
        parent::__construct($annotationReader, FormField::class);
    }

    /**
     * Build creation fields configuration.
     *
     * @param ReflectionClass $class      Entity class.
     * @param FormMapper      $formMapper Admin form mapper.
     *
     * @return void
     */
    public function configureCreateFields(
        ReflectionClass $class,
        FormMapper $formMapper
    ): void {
        $this->configureReaderFields(
            $class,
            $formMapper,
            $this->annotationClass,
            FormField::ACTION_CREATE
        );
    }

    /**
     * Build edit fields configuration.
     *
     * @param ReflectionClass $class      Entity class.
     * @param FormMapper      $formMapper Admin form mapper.
     *
     * @return void
     */
    public function configureEditFields(
        ReflectionClass $class,
        FormMapper $formMapper
    ): void {
        $this->configureReaderFields(
            $class,
            $formMapper,
            $this->annotationClass,
            FormField::ACTION_EDIT
        );
    }

}
