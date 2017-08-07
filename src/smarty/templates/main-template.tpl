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