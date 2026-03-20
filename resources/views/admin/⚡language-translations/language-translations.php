<?php

use function Livewire\Volt\{state, rules, compute, on, mount, layout};
use App\Models\Language;
use Spatie\TranslationLoader\LanguageLine;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class LanguageTranslations (Livewire 4 Multi-File Component)
 *
 * This component provides an interface for inline editing 
 * of translation keys for a specific language.
 */
new #[Layout('layouts::admin', ['title' => 'Manage Translations'])] class extends Component {
    use WithPagination;

    /** @var Language The target language being translated. */
    public Language $language;

    /** @var string Search query for filtering translations. */
    public string $search = '';

    /** @var array Form data for inline editing: ['line_id' => 'translation_text'] */
    public array $translations = [];

    /** @var string|null Focus state for active inline editor */
    public ?string $editingKey = null;

    /**
     * Initializes the component with the target language.
     * @param Language $language
     */
    public function mount(Language $language)
    {
        $this->language = $language;
        $this->loadTranslations();
    }

    /**
     * Loads current translations into the inline editing array.
     */
    public function loadTranslations()
    {
        $lines = $this->translationLines;
        foreach ($lines as $line) {
            $this->translations[$line->id] = $line->text[$this->language->code] ?? '';
        }
    }

    /**
     * Hooks into search changes to reload inline editing state.
     */
    public function updatedSearch()
    {
        $this->resetPage();
        $this->loadTranslations();
    }

    /**
     * Hooks into pagination changes to reload inline editing state.
     */
    public function updatedPage()
    {
        $this->loadTranslations();
    }

    /**
     * Saves a specific translation inline when it loses focus or user hits enter.
     * 
     * @param int $id The LanguageLine ID
     */
    public function saveTranslation(int $id)
    {
        $line = LanguageLine::find($id);

        if ($line) {
            $text = $line->text;
            $newValue = $this->translations[$id] ?? '';

            // If empty, we can choose to remove the key for this language
            if (empty(trim($newValue))) {
                unset($text[$this->language->code]);
            } else {
                $text[$this->language->code] = $newValue;
            }

            $line->text = $text;
            $line->save();

            session()->flash('success', 'Translation updated successfully!');
        }

        $this->editingKey = null;
    }

    /** Delete confirmation state */
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    /**
     * Opens the deletion confirmation modal mapping to the target key ID.
     *
     * @param int $id The ID of the LanguageLine to discard.
     */
    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Delete a translation line completely after confirmation.
     */
    public function deleteTranslation()
    {
        if ($this->deletingId) {
            LanguageLine::destroy($this->deletingId);
            session()->flash('success', 'Translation key deleted');
            $this->showDeleteModal = false;
            $this->deletingId = null;
            $this->loadTranslations();
        }
    }

    /**
     * Open model to create a new key.
     */
    public bool $showCreateModal = false;
    public string $newGroup = '';
    public string $newKey = '';
    public string $newEnglishText = '';
    public string $newTargetText = '';

    public function rules()
    {
        return [
            'newGroup' => 'required|string|max:255',
            'newKey' => 'required|string|max:255',
            'newEnglishText' => 'required|string',
        ];
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->newGroup = '';
        $this->newKey = '';
        $this->newEnglishText = '';
        $this->newTargetText = '';
        $this->showCreateModal = true;
    }

    public function saveNewKey()
    {
        $this->validate();

        // Check if key already exists in the group
        $existing = LanguageLine::where('group', $this->newGroup)
            ->where('key', $this->newKey)
            ->first();
        if ($existing) {
            $this->addError('newKey', 'This key already exists in the specified group.');
            return;
        }

        $text = [
            'en' => $this->newEnglishText
        ];

        if (!empty(trim($this->newTargetText))) {
            $text[$this->language->code] = $this->newTargetText;
        }

        LanguageLine::create([
            'group' => $this->newGroup,
            'key' => $this->newKey,
            'text' => $text,
        ]);

        session()->flash('success', 'New translation key added');
        $this->showCreateModal = false;
        $this->loadTranslations();
    }


    /**
     * Imports translations from Laravel's lang directory (.php and .json files)
     */
    public function importFromFiles()
    {
        $locales = array_unique(['en', $this->language->code]);
        $addedCount = 0;
        $updatedCount = 0;

        foreach ($locales as $locale) {
            $path = app()->langPath() . '/' . $locale;

            // 1. PHP files in lang/locale/ directory
            if (\Illuminate\Support\Facades\File::isDirectory($path)) {
                $files = \Illuminate\Support\Facades\File::allFiles($path);
                foreach ($files as $file) {
                    if ($file->getExtension() === 'php') {
                        $group = $file->getFilenameWithoutExtension();
                        $translations = require $file->getPathname();
                        if (is_array($translations)) {
                            $this->importArray($translations, $group, $locale, $addedCount, $updatedCount);
                        }
                    }
                }
            }

            // 2. JSON files in lang/locale.json
            $jsonPath = app()->langPath() . '/' . $locale . '.json';
            if (\Illuminate\Support\Facades\File::exists($jsonPath)) {
                $translations = json_decode(\Illuminate\Support\Facades\File::get($jsonPath), true);
                if (is_array($translations)) {
                    $this->importArray($translations, '*', $locale, $addedCount, $updatedCount);
                }
            }
        }

        if ($addedCount > 0 || $updatedCount > 0) {
            session()->flash('success', __('admin.language_translations.import_success', ['added' => $addedCount, 'updated' => $updatedCount]));
        } else {
            session()->flash('success', __('admin.language_translations.import_empty'));
        }

        $this->loadTranslations();
    }

    /**
     * Helper to recursively import translation arrays
     */
    protected function importArray(array $translations, string $group, string $locale, int &$addedCount, int &$updatedCount, string $prefix = '')
    {
        foreach ($translations as $key => $value) {
            $fullKey = $prefix ? $prefix . '.' . $key : $key;

            if (is_array($value)) {
                $this->importArray($value, $group, $locale, $addedCount, $updatedCount, $fullKey);
            } else {
                $value = (string) $value;
                $line = LanguageLine::where('group', $group)->where('key', $fullKey)->first();

                if (!$line) {
                    LanguageLine::create([
                        'group' => $group,
                        'key' => $fullKey,
                        'text' => [$locale => $value]
                    ]);
                    $addedCount++;
                } else {
                    $text = is_array($line->text) ? $line->text : [];
                    if (!array_key_exists($locale, $text) || $text[$locale] !== $value) {
                        $text[$locale] = $value;
                        $line->text = $text;
                        $line->update(['text' => $text]);
                        $updatedCount++;
                    }
                }
            }
        }
    }

    /**
     * Computed property for the paginated or filtered list of translation lines.
     */
    public function getTranslationLinesProperty()
    {
        return LanguageLine::query()
            ->when($this->search, function ($query) {
                $query->where('group', 'like', "%{$this->search}%")
                    ->orWhere('key', 'like', "%{$this->search}%")
                    ->orWhere('text', 'like', "%{$this->search}%");
            })
            ->orderBy('group')
            ->orderBy('key')
            ->paginate(50);
    }

    public function with(): array
    {
        return [
            'lines' => $this->translationLines,
            'targetLanguage' => $this->language,
        ];
    }
};
