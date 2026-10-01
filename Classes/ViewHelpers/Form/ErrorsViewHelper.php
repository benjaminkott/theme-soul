<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\ViewHelpers\Form;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;
use TYPO3\CMS\Form\Service\TranslationService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * What stopped a form, as the list `sds-form-errors` takes: each message in
 * the words the form gives it, and the id of the field it belongs to.
 *
 *   {soul:form.errors(results: validationResults, form: form)}
 *
 * The form framework's translation is asked for here and not injected: a
 * site without the form framework has no such service, and never renders
 * this.
 */
final class ErrorsViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('results', Result::class, 'The validation results of the form', false);
        $this->registerArgument('form', FormRuntime::class, 'The form', true);
    }

    /**
     * @return list<array{message: string, for: string}>
     */
    public function render(): array
    {
        $results = $this->arguments['results'];
        $form = $this->arguments['form'];
        if (!$results instanceof Result || !$form instanceof FormRuntime) {
            return [];
        }

        $translation = GeneralUtility::makeInstance(TranslationService::class);
        $errors = [];
        foreach ($results->getFlattenedErrors() as $path => $list) {
            $parts = explode('.', $path);
            $identifier = end($parts);
            $element = $form->getFormDefinition()->getElementByIdentifier($identifier);
            foreach ($list as $error) {
                $message = $element === null
                    ? $error->getMessage()
                    : $translation->translateFormElementError($element, $error->getCode(), $error->getArguments(), (string)$error, $form);
                $errors[] = ['message' => $message, 'for' => $element?->getUniqueIdentifier() ?? ''];
            }
        }

        return $errors;
    }
}
