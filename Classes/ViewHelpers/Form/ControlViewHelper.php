<?php

declare(strict_types=1);

namespace TYPO3\Soul\Theme\ViewHelpers\Form;

use TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper;
use TYPO3\Soul\Theme\Html\Attributes;

/**
 * A control of the system as a field of an Extbase form.
 *
 *   <soul:form.control tag="sds-field" property="{element.identifier}" of="{label: label, type: 'email'}" />
 *
 * The name and the value come from the form, as they do for the core's own
 * fields: the submitted value after an error, the object's value before.
 * The name goes into the request token, or the form refuses the field. The
 * element writes a named native control into the page, and that control is
 * what the browser submits.
 *
 * `kind` says what the value is: `value` a string, `check` one checkbox,
 * `checks` the values of a group, `file` nothing to show again.
 * `options` are the answers, value to label, as the element takes them.
 * The label, the hint, the error and the native attributes of the form
 * definition go to the element where it has an attribute for them.
 */
final class ControlViewHelper extends AbstractFormFieldViewHelper
{
    /** What each element takes besides its name and value. A field's
        visible label is its `caption`; `label` names a bare field only. */
    private const TAKES = [
        'sds-field' => ['required', 'caption', 'hint', 'error', 'fieldId', 'min', 'max', 'step', 'maxlength', 'pattern', 'autocomplete', 'inputmode'],
        'sds-textarea' => ['required', 'caption', 'hint', 'error', 'fieldId', 'maxlength', 'autocomplete', 'rows'],
        'sds-select' => ['required', 'caption', 'hint', 'error', 'fieldId'],
        'sds-file' => ['required', 'caption', 'hint', 'error', 'fieldId', 'accept'],
        'sds-checkbox' => ['required', 'label', 'hint'],
        'sds-switch' => ['label', 'hint'],
        'sds-range' => ['caption', 'hint', 'fieldId', 'min', 'max', 'step'],
        'sds-checkbox-group' => ['legend', 'hint'],
        'sds-radio' => ['required', 'legend', 'hint'],
    ];

    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('tag', 'string', 'The element', true);
        $this->registerArgument('of', 'array', 'The attributes, by name in camel case', false, []);
        $this->registerArgument('flags', 'array', 'The boolean attributes, by name in camel case', false, []);
        $this->registerArgument('kind', 'string', 'value, check, checks or file', false, 'value');
        $this->registerArgument('options', 'array', 'The answers, value to label', false, []);
        $this->registerArgument('label', 'string', 'What the field asks', false, '');
        $this->registerArgument('hint', 'string', 'What the field says about its answer', false, '');
        $this->registerArgument('error', 'string', 'Why the answer was refused', false, '');
        $this->registerArgument('fieldId', 'string', 'The id of the native control', false, '');
        $this->registerArgument('required', 'bool', 'If the form refuses an empty answer', false, false);
        $this->registerArgument('native', 'array', 'Native attributes of the form definition', false, []);
    }

    public function render(): string
    {
        $tag = Attributes::tag((string)$this->arguments['tag']);
        $this->setRespectSubmittedDataValue(true);
        $name = $this->getName();
        $takes = self::TAKES[$tag] ?? [];
        $given = [
            'label' => $this->arguments['label'],
            'caption' => $this->arguments['label'],
            'legend' => $this->arguments['label'],
            'hint' => $this->arguments['hint'],
            'error' => $this->arguments['error'],
            'fieldId' => $this->arguments['fieldId'],
        ] + $this->arguments['native'];
        $attributes = array_intersect_key($given, array_flip($takes)) + $this->arguments['of'];
        if (in_array('required', $takes, true)) {
            $attributes['required'] = (bool)$this->arguments['required'];
        }
        foreach ($this->arguments['flags'] as $flag => $on) {
            $attributes[$flag] = (bool)$on;
        }
        $hidden = '';

        switch ($this->arguments['kind']) {
            case 'check':
                $value = (string)($this->getValueAttribute() ?? '1');
                $attributes['value'] = $value === '' ? '1' : $value;
                $attributes['checked'] = (bool)$this->current();
                $hidden = $this->renderHiddenFieldForEmptyValue();
                break;
            case 'checks':
                $name .= '[]';
                $attributes['values'] = array_values(array_map('strval', (array)$this->current()));
                $hidden = $this->renderHiddenFieldForEmptyValue();
                break;
            case 'file':
                if (($attributes['multiple'] ?? false) === true) {
                    $name .= '[]';
                }
                break;
            default:
                $value = $this->getValueAttribute();
                $attributes['value'] = is_scalar($value) ? (string)$value : '';
        }

        $options = [];
        foreach ($this->arguments['options'] as $value => $label) {
            $options[] = ['label' => (string)$label, 'value' => (string)$value];
        }
        if ($options !== []) {
            $attributes[$tag === 'sds-select' ? 'options' : 'choices'] = $options;
        }

        $this->registerFieldNameForFormTokenGeneration($name);

        return $hidden . sprintf('<%s %s></%s>', $tag, Attributes::write(['name' => $name] + $attributes), $tag);
    }

    /**
     * What the field holds now: what was submitted where the form came back
     * with an error, the object's value otherwise.
     */
    private function current(): mixed
    {
        $value = $this->hasMappingErrorOccurred() ? $this->getLastSubmittedFormData() : $this->getPropertyValue();
        if ($value instanceof \Traversable) {
            $value = iterator_to_array($value);
        }

        return is_array($value) ? array_map($this->convertToPlainValue(...), $value) : $value;
    }
}
