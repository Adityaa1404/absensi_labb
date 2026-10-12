<?php

namespace App\Models;

use Core\Database;

class Setting
{
    /**
     * Ambil 1 nilai setting berdasarkan key
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $row = Database::fetch(
            "SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1",
            ['key' => $key]
        );

        return $row ? $row['setting_value'] : $default;
    }

    /**
     * Ambil beberapa setting sekaligus dalam format key => value
     */
    public static function getMultiple(array $keys): array
    {
        if (empty($keys)) {
            return [];
        }

        $placeholders = [];
        $params = [];
        foreach ($keys as $i => $k) {
            $ph = ":k{$i}";
            $placeholders[] = $ph;
            $params["k{$i}"] = $k;
        }

        $sql = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN (" . implode(',', $placeholders) . ")";
        $rows = Database::fetchAll($sql, $params);

        $result = [];
        foreach ($keys as $k) {
            $result[$k] = null;
        }
        foreach ($rows as $row) {
            $result[$row['setting_key']] = $row['setting_value'];
        }

        return $result;
    }

    /**
     * Simpan atau perbarui 1 data setting
     */
    public static function set(string $key, ?string $value, ?string $label = null): void
    {
        $existing = Database::fetch(
            "SELECT id_setting FROM settings WHERE setting_key = :key LIMIT 1",
            ['key' => $key]
        );

        if ($existing) {
            $sql = "UPDATE settings SET setting_value = :value" . ($label !== null ? ", setting_label = :label" : "") . " WHERE setting_key = :key";
            $params = ['value' => $value, 'key' => $key];
            if ($label !== null) {
                $params['label'] = $label;
            }
            Database::query($sql, $params);
        } else {
            Database::query(
                "INSERT INTO settings (setting_key, setting_value, setting_label) VALUES (:key, :value, :label)",
                ['key' => $key, 'value' => $value, 'label' => $label ?? $key]
            );
        }
    }

    /**
     * Update banyak data setting sekaligus dari array [key => value]
     */
    public static function updateBatch(array $data): void
    {
        foreach ($data as $key => $value) {
            self::set((string)$key, is_null($value) ? null : (string)$value);
        }
    }

    /**
     * Ambil seluruh baris setting untuk halaman manajemen Super Admin
     */
    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM settings ORDER BY id_setting ASC");
    }

    /**
     * Ambil semua settings dalam bentuk key => value dictionary
     */
    public static function allMap(): array
    {
        $rows = self::all();
        $map = [];
        foreach ($rows as $r) {
            $map[$r['setting_key']] = $r['setting_value'];
        }
        return $map;
    }
}
