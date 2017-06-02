<?php
/**
 * This file is used for IDE code completion
 */

namespace {
{{foreach from=$backwardsCompatibleClasses item=class}}

    {{include file="backwards-compatible-class-template.tpl" class=$class }}
{{/foreach}}

    exit("This file should not be included, only analyzed by your IDE");
}

namespace PHPSTORM_OXID_META {
    // this is legacy format for 2016.1 and EARLIER
    // This file is not a CODE, it makes no sense and won't run or validate
    // Its AST serves IDE as DATA source to make advanced type inference decisions.

    $STATIC_METHOD_TYPES = [
        // we make sections for scopes
        \oxNew('') => [
{{foreach from=$backwardsCompatibleClasses item=class}}

        '\{{$class.childClassName}}' => \{{$class.parentClassName}}::class,
{{/foreach}}
        ],
    ];
}
