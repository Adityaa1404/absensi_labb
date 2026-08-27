<?php

namespace Core;

class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Jalankan aturan validasi
     * Contoh: ['email' => 'required|email', 'password' => 'required|min:6']
     */
    public function rules(array $rules, array $customMessages = []): self
    {
        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            $fieldValue = trim((string)($this->data[$field] ?? ''));
            $fieldLabel = ucwords(str_replace(['_', '-'], ' ', $field));

            foreach ($ruleList as $rule) {
                $ruleParams = [];
                if (str_contains($rule, ':')) {
                    [$ruleName, $paramStr] = explode(':', $rule, 2);
                    $ruleParams = explode(',', $paramStr);
                } else {
                    $ruleName = $rule;
                }

                match ($ruleName) {
                    'required' => $this->validateRequired($field, $fieldValue, $fieldLabel, $customMessages),
                    'email'    => $this->validateEmail($field, $fieldValue, $fieldLabel, $customMessages),
                    'min'      => $this->validateMin($field, $fieldValue, (int)($ruleParams[0] ?? 0), $fieldLabel, $customMessages),
                    'max'      => $this->validateMax($field, $fieldValue, (int)($ruleParams[0] ?? 0), $fieldLabel, $customMessages),
                    'numeric'  => $this->validateNumeric($field, $fieldValue, $fieldLabel, $customMessages),
                    'matches'  => $this->validateMatches($field, $fieldValue, $ruleParams[0] ?? '', $fieldLabel, $customMessages),
                    'in'       => $this->validateIn($field, $fieldValue, $ruleParams, $fieldLabel, $customMessages),
                    default    => null
                };
            }
        }

        return $this;
    }

    private function validateRequired(string $field, string $val, string $label, array $custom): void
    {
        if ($val === '') {
            $this->addError($field, $custom["{$field}.required"] ?? "Kolom {$label} wajib diisi.");
        }
    }

    private function validateEmail(string $field, string $val, string $label, array $custom): void
    {
        if ($val !== '' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $custom["{$field}.email"] ?? "Format {$label} tidak valid.");
        }
    }

    private function validateMin(string $field, string $val, int $min, string $label, array $custom): void
    {
        if ($val !== '' && mb_strlen($val) < $min) {
            $this->addError($field, $custom["{$field}.min"] ?? "{$label} minimal terdiri dari {$min} karakter.");
        }
    }

    private function validateMax(string $field, string $val, int $max, string $label, array $custom): void
    {
        if ($val !== '' && mb_strlen($val) > $max) {
            $this->addError($field, $custom["{$field}.max"] ?? "{$label} maksimal terdiri dari {$max} karakter.");
        }
    }

    private function validateNumeric(string $field, string $val, string $label, array $custom): void
    {
        if ($val !== '' && !is_numeric($val)) {
            $this->addError($field, $custom["{$field}.numeric"] ?? "{$label} harus berupa angka.");
        }
    }

    private function validateMatches(string $field, string $val, string $matchField, string $label, array $custom): void
    {
        $matchVal = trim((string)($this->data[$matchField] ?? ''));
        $matchLabel = ucwords(str_replace(['_', '-'], ' ', $matchField));
        if ($val !== $matchVal) {
            $this->addError($field, $custom["{$field}.matches"] ?? "{$label} tidak cocok dengan {$matchLabel}.");
        }
    }

    private function validateIn(string $field, string $val, array $allowed, string $label, array $custom): void
    {
        if ($val !== '' && !in_array($val, $allowed, true)) {
            $this->addError($field, $custom["{$field}.in"] ?? "Nilai {$label} tidak valid.");
        }
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        foreach ($this->errors as $fieldErrors) {
            if (!empty($fieldErrors)) {
                return $fieldErrors[0];
            }
        }
        return null;
    }

    /**
     * Helper cepat untuk flash semua error ke Guard
     */
    public function flashErrors(): void
    {
        foreach ($this->errors as $fieldErrors) {
            foreach ($fieldErrors as $err) {
                Guard::setFlash('error', $err);
            }
        }
    }
}
