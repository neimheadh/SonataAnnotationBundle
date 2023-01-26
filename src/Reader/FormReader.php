<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Reader;

use KunicMarko\SonataAnnotationBundle\Annotation\FormField;
use KunicMarko\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use ReflectionClass;
use Sonata\AdminBundle\Form\FormMapper;

use function array_key_exists;
use function array_merge;
use function ksort;

/**
 * Form configuration reader.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
final class FormReader
{

    use AnnotationReaderTrait;

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
        $this->configureFields(
          $class,
          $formMapper,
          FormField::ACTION_EDIT
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
        $this->configureFields($class, $formMapper, FormField::ACTION_CREATE);
    }

    /**
     * Build fields configuration.
     *
     * @param ReflectionClass $class      Entity class.
     * @param FormMapper      $formMapper Admin form mapper.
     * @param string          $action     Current action.
     *
     * @return void
     */
    private function configureFields(
      ReflectionClass $class,
      FormMapper $formMapper,
      string $action
    ): void {
        $propertiesWithPosition = [];
        $propertiesWithoutPosition = [];

        foreach ($class->getProperties() as $property) {
            foreach ($this->getPropertyAnnotations($property) as $annotation) {
                if (!$annotation instanceof FormField) {
                    continue;
                }

                if (!isset($annotation->action)) {
                    throw new MissingAnnotationArgumentException(
                      $annotation,
                      'action'
                    );
                }

                if ($annotation->action !== $action) {
                    continue;
                }

                if (!isset($annotation->position)) {
                    $propertiesWithoutPosition[] = [
                      'name' => $property->getName(),
                      'settings' => $annotation->getSettings(),
                    ];

                    continue;
                }

                if (array_key_exists(
                  $annotation->position,
                  $propertiesWithPosition
                )) {
                    throw new \InvalidArgumentException(
                      sprintf(
                        'Position "%s" is already in use by "%s", try setting a different position for "%s".',
                        $annotation->position,
                        $propertiesWithPosition[$annotation->position]['name'],
                        $property->getName()
                      )
                    );
                }

                $propertiesWithPosition[$annotation->position] = [
                  'name' => $property->getName(),
                  'settings' => $annotation->getSettings(),
                ];
            }
        }

        ksort($propertiesWithPosition);

        $properties = array_merge(
          $propertiesWithPosition,
          $propertiesWithoutPosition
        );

        foreach ($properties as $property) {
            $formMapper->add($property['name'], ...$property['settings']);
        }
    }

}
