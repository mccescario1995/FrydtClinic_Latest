{{-- Reusable Form Section Components --}}

{{-- Personal Information Section --}}
@php
    $personalFields = [
        'name' => ['label' => 'Full Name', 'type' => 'text', 'required' => true],
        'email' => ['label' => 'Email Address', 'type' => 'email', 'required' => true],
        'phone' => ['label' => 'Phone Number', 'type' => 'text', 'required' => false],
        'birth_date' => ['label' => 'Date of Birth', 'type' => 'date', 'required' => false],
        'gender' => ['label' => 'Gender', 'type' => 'select', 'options' => ['male' => 'Male', 'female' => 'Female'], 'required' => false],
        'civil_status' => ['label' => 'Civil Status', 'type' => 'select', 'options' => ['single' => 'Single', 'married' => 'Married', 'widowed' => 'Widowed', 'separated' => 'Separated'], 'required' => false],
        'address' => ['label' => 'Address', 'type' => 'textarea', 'required' => false],
    ];
@endphp

{{-- Contact Information Section --}}
@php
    $contactFields = [
        'emergency_contact_name' => ['label' => 'Emergency Contact Name', 'type' => 'text', 'required' => false],
        'emergency_contact_relationship' => ['label' => 'Relationship', 'type' => 'text', 'required' => false],
        'emergency_contact_phone' => ['label' => 'Emergency Contact Phone', 'type' => 'text', 'required' => false],
    ];
@endphp

{{-- Medical Information Section --}}
@php
    $medicalFields = [
        'blood_type' => ['label' => 'Blood Type', 'type' => 'text', 'readonly' => true],
        'occupation' => ['label' => 'Occupation', 'type' => 'text', 'readonly' => true],
        'religion' => ['label' => 'Religion', 'type' => 'text', 'readonly' => true],
        'barangay_captain' => ['label' => 'Barangay Captain', 'type' => 'text', 'readonly' => true],
    ];
@endphp

{{-- PhilHealth Information Section --}}
@php
    $philhealthFields = [
        'philhealth_membership' => ['label' => 'PhilHealth Membership', 'type' => 'select', 'options' => ['member' => 'Member', 'dependent' => 'Dependent'], 'readonly' => true],
        'philhealth_number' => ['label' => 'PhilHealth Number', 'type' => 'text', 'readonly' => true],
    ];
@endphp

{{-- Security Settings Section --}}
@php
    $securityFields = [
        'user_pin' => ['label' => 'PIN (6 digits)', 'type' => 'password', 'maxlength' => 6, 'pattern' => '[0-9]{6}', 'inputmode' => 'numeric', 'required' => false],
        'user_pin_confirmation' => ['label' => 'Confirm PIN', 'type' => 'password', 'maxlength' => 6, 'pattern' => '[0-9]{6}', 'inputmode' => 'numeric', 'required' => false],
    ];
@endphp

{{-- Form Field Component --}}
@php
function renderFormField($fieldName, $fieldConfig, $oldValue = null, $errors = null, $theme = 'patient') {
    $required = $fieldConfig['required'] ?? false;
    $readonly = $fieldConfig['readonly'] ?? false;
    $label = $fieldConfig['label'];
    $type = $fieldConfig['type'];
    $options = $fieldConfig['options'] ?? [];
    $attributes = '';

    // Add additional attributes
    if (isset($fieldConfig['maxlength'])) $attributes .= " maxlength=\"{$fieldConfig['maxlength']}\"";
    if (isset($fieldConfig['pattern'])) $attributes .= " pattern=\"{$fieldConfig['pattern']}\"";
    if (isset($fieldConfig['inputmode'])) $attributes .= " inputmode=\"{$fieldConfig['inputmode']}\"";

    $fieldHtml = '';

    // Label
    $requiredMark = $required ? ' <span class="text-danger">*</span>' : '';
    $fieldHtml .= "<label for=\"{$fieldName}\" class=\"form-label\">{$label}{$requiredMark}</label>";

    // Field
    if ($type === 'select') {
        $fieldHtml .= "<select name=\"{$fieldName}\" id=\"{$fieldName}\" class=\"form-select" . ($errors && $errors->has($fieldName) ? ' is-invalid' : '') . "\"" . ($required ? ' required' : '') . ($readonly ? ' readonly' : '') . ">";
        $fieldHtml .= "<option value=\"\">Select {$label}</option>";
        foreach ($options as $value => $text) {
            $selected = (old($fieldName) ?? $oldValue) == $value ? ' selected' : '';
            $fieldHtml .= "<option value=\"{$value}\"{$selected}>{$text}</option>";
        }
        $fieldHtml .= "</select>";
    } elseif ($type === 'textarea') {
        $fieldHtml .= "<textarea name=\"{$fieldName}\" id=\"{$fieldName}\" class=\"form-control" . ($errors && $errors->has($fieldName) ? ' is-invalid' : '') . "\" rows=\"3\"" . ($required ? ' required' : '') . ($readonly ? ' readonly' : '') . ">" . (old($fieldName) ?? $oldValue ?? '') . "</textarea>";
    } else {
        $fieldHtml .= "<input type=\"{$type}\" name=\"{$fieldName}\" id=\"{$fieldName}\" class=\"form-control" . ($errors && $errors->has($fieldName) ? ' is-invalid' : '') . "\" value=\"" . (old($fieldName) ?? $oldValue ?? '') . "\"" . ($required ? ' required' : '') . ($readonly ? ' readonly' : '') . $attributes . ">";
    }

    // Error display
    if ($errors && $errors->has($fieldName)) {
        $fieldHtml .= "<div class=\"invalid-feedback\">{$errors->first($fieldName)}</div>";
    }

    return $fieldHtml;
}
@endphp
