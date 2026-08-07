<?php

declare(strict_types=1);

function adminEscape(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function adminChecked(mixed $value): string
{
    return (int) $value === 1 ? 'checked' : '';
}

function adminSelected(mixed $left, mixed $right): string
{
    return (string) $left === (string) $right ? 'selected' : '';
}

function adminCategoryLabel(array $category): string
{
    return (string) ($category['name'] ?? '');
}

function adminEntityLabel(array $entity): string
{
    $type = trim((string) ($entity['entity_type'] ?? ''));
    $categoryName = trim((string) ($entity['category_name'] ?? ''));
    $suffix = $type !== '' ? sprintf(' · %s', $type) : '';

    if ($categoryName !== '') {
        return sprintf('%s (%s)%s', (string) $entity['name'], $categoryName, $suffix);
    }

    return sprintf('%s%s', (string) $entity['name'], $suffix);
}

function adminCollectionLabel(array $collection): string
{
    $entityName = trim((string) ($collection['entity_name'] ?? ''));

    if ($entityName === '') {
        return (string) ($collection['name'] ?? '');
    }

    return sprintf('%s (%s)', (string) $collection['name'], $entityName);
}

function adminPieceLabel(array $piece): string
{
    $collectionName = trim((string) ($piece['collection_name'] ?? ''));

    if ($collectionName === '') {
        return (string) ($piece['name'] ?? '');
    }

    return sprintf('%s (%s)', (string) ($piece['name'] ?? ''), $collectionName);
}

function adminFirstNonEmpty(array $values, string $fallback = ''): string
{
    foreach ($values as $value) {
        $normalized = trim((string) $value);

        if ($normalized !== '') {
            return $normalized;
        }
    }

    return $fallback;
}

function adminRecordPreviewImage(array $record, array $keys): string
{
    $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
    $basePath = trim(str_replace('\\', '/', dirname(dirname($scriptName))), '/');
    $publicPrefix = $basePath === '' ? '/public/' : '/' . $basePath . '/public/';

    foreach ($keys as $key) {
        $value = trim((string) ($record[$key] ?? ''));

        if ($value !== '') {
            if (
                str_starts_with($value, 'http://')
                || str_starts_with($value, 'https://')
                || str_starts_with($value, 'data:')
                || str_starts_with($value, '/')
                || str_starts_with($value, '../')
            ) {
                return $value;
            }

            if (str_starts_with($value, './')) {
                return $publicPrefix . substr($value, 2);
            }

            return $publicPrefix . ltrim($value, '/');
        }
    }

    return '';
}

function adminFormatDateLabel(?string $value): string
{
    if ($value === null || trim($value) === '') {
        return 'Sin fecha';
    }

    $timestamp = strtotime($value);

    if ($timestamp === false) {
        return $value;
    }

    $months = ['ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sep.', 'oct.', 'nov.', 'dic.'];

    return sprintf(
        '%d %s %d, %02d:%02d',
        (int) date('j', $timestamp),
        $months[(int) date('n', $timestamp) - 1],
        (int) date('Y', $timestamp),
        (int) date('H', $timestamp),
        (int) date('i', $timestamp)
    );
}

function adminSectionTypeOptions(): array
{
    return [
        'hero' => 'Hero',
        'intro' => 'Introduccion',
        'pieces' => 'Piezas',
        'gallery' => 'Galeria',
        'technical_details' => 'Detalles tecnicos',
        'full_image' => 'Imagen completa',
    ];
}

function adminSectionTypeLabel(string $type): string
{
    $options = adminSectionTypeOptions();

    return $options[$type] ?? $type;
}

function adminFormatJsonForTextarea(?string $value): string
{
    if ($value === null || trim($value) === '') {
        return '';
    }

    try {
        $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

        return (string) json_encode(
            $decoded,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    } catch (JsonException) {
        return $value;
    }
}

function adminRenderSectionVisualSettings(string $activeType, ?string $settingsJson = null): string
{
    $schema = adminSectionVisualSchema();
    ob_start();
    ?>
    <div class="admin-visual-settings" data-section-visual-settings>
      <div class="admin-visual-settings-heading">
        <div>
          <span>Configuracion visual</span>
          <strong>Aspecto del componente</strong>
        </div>
        <small>Los ajustes se guardan automaticamente en settings_json.</small>
      </div>

      <?php foreach ($schema as $sectionType => $definition): ?>
        <?php
        $values = $sectionType === $activeType
            ? adminDecodeSectionVisualSettings($settingsJson, $sectionType)
            : adminSectionVisualDefaults($sectionType);
        ?>
        <fieldset
          class="admin-visual-settings-panel"
          data-section-settings-panel="<?= adminEscape($sectionType) ?>"
          <?= $sectionType === $activeType ? '' : 'hidden' ?>
        >
          <legend><?= adminEscape((string) $definition['label']) ?></legend>
          <p><?= adminEscape((string) $definition['description']) ?></p>
          <div class="admin-visual-settings-grid">
            <?php foreach ($definition['fields'] as $key => $field): ?>
              <?php if ($field['type'] === 'checkbox'): ?>
                <label class="admin-visual-toggle">
                  <input
                    type="checkbox"
                    name="visual_settings[<?= adminEscape($sectionType) ?>][<?= adminEscape($key) ?>]"
                    value="1"
                    <?= !empty($values[$key]) ? 'checked' : '' ?>
                  />
                  <span><?= adminEscape((string) $field['label']) ?></span>
                </label>
              <?php else: ?>
                <label class="admin-visual-field">
                  <span><?= adminEscape((string) $field['label']) ?></span>
                  <select name="visual_settings[<?= adminEscape($sectionType) ?>][<?= adminEscape($key) ?>]">
                    <?php foreach ($field['options'] as $optionValue => $optionLabel): ?>
                      <option value="<?= adminEscape((string) $optionValue) ?>" <?= adminSelected($values[$key] ?? '', $optionValue) ?>><?= adminEscape((string) $optionLabel) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </fieldset>
      <?php endforeach; ?>
    </div>
    <?php

    return (string) ob_get_clean();
}
