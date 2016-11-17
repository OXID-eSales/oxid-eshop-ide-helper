<?php
/**
 * This file is used for IDE code completion
 */

namespace {
  exit("This file should not be included, only analyzed by your IDE");
}

{{foreach from=$nameSpaces key=namespace item=classes}}

namespace {{$namespace}}
{
{{foreach from=$classes item=class}}
    {{include file="class-template.tpl" class=$class }}
{{/foreach}}
}
{{/foreach}}

