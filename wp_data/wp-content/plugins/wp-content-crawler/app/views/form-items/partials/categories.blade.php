{{--
    Required variables:
        string      $name:          Name of the form item
        array       $categories:    Category data retrieved from Utils::getCategories
        int|null    $selectedId:    Selected category ID

    Optional variables:
        string|null $taxonomyInputName: Name of the hidden input that stores the selected category's taxonomy.
        bool        $addTaxonomyInput:  True if a hidden taxonomy input should be added.
--}}
<?php
    /** @var array $categories */
    $selectedTaxonomy = isset($categories[0]) && $categories[0]['taxonomy'] ? $categories[0]['taxonomy'] : 'category';
    $addTaxonomyInput = isset($addTaxonomyInput) && $addTaxonomyInput;
    $taxonomyInputName = isset($taxonomyInputName) && $taxonomyInputName ? $taxonomyInputName : null;
?>
<select name="{{ $name }}" id="{{ $name }}">
    @foreach($categories as $categoryData)
        <?php
            /** @var array $categoryData */
            $categoryId         = $categoryData['id'];
            $categoryName       = $categoryData['name'];
            $categoryTaxonomy   = $categoryData['taxonomy'];
            $isSelected         = isset($selectedId) && $selectedId && $categoryId == $selectedId;
        ?>
        <option value="{{ $categoryId }}" data-taxonomy="{{ $categoryTaxonomy }}"
                @if($isSelected) selected="selected" @endif>{!! $categoryName !!}</option>

        {{-- If the selected taxonomy should be shown and this is the selected category ID, set its taxonomy as selected. --}}
        @if($addTaxonomyInput && $isSelected)
            <?php
                $selectedTaxonomy = $categoryTaxonomy;
            ?>
        @endif

    @endforeach
</select>

{{-- Add the taxonomy input if it is required --}}
@if($addTaxonomyInput && $taxonomyInputName)
    <input type="hidden" class="category-taxonomy" name="{{ $taxonomyInputName }}" value="{{ $selectedTaxonomy }}">
@endif