<?php

declare(strict_types=1);

namespace KunicMarko\SonataAnnotationBundle\Reader;

use Exception;
use KunicMarko\SonataAnnotationBundle\Annotation\AbstractAction;
use KunicMarko\SonataAnnotationBundle\Exception\MissingAnnotationArgumentException;
use ReflectionClass;

/**
 * Action reader main class.
 *
 * @author Marko Kunic <kunicmarko20@gmail.com>
 * @author Mathieu Wambre <contact@neimheadh.fr>
 */
abstract class AbstractActionReader
{

    use AnnotationReaderTrait;

    /**
     * Get action list.
     *
     * @param ReflectionClass $class   Entity class.
     * @param array           $actions Action list.
     *
     * @return array
     * @throws MissingAnnotationArgumentException if a template attribute is
     *                                            missing.
     * @throws Exception if an appropriate source of randomness cannot be
     *                   found.
     */
    public function getActions(ReflectionClass $class, array $actions): array
    {
        foreach ($this->getClassAnnotations($class) as $annotation) {
            if ($this->isSupported($annotation)) {
                if (!isset($annotation->template)) {
                    throw new MissingAnnotationArgumentException(
                        $annotation,
                        'template',
                        $class
                    );
                }

                /** @var AbstractAction $annotation */
                $actions[random_int(
                    -99999,
                    99999
                )]['template'] = $annotation->template;
            }
        }

        return $actions;
    }

    /**
     * Is the given annotation supported.
     *
     * @param object $annotation Annotation.
     *
     * @return bool
     */
    abstract protected function isSupported(object $annotation): bool;

}
