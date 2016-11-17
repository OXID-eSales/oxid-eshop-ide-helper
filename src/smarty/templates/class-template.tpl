
    {{if $class.isInterface}}interface{{elseif $class.isAbstract}}abstract class{{else}}class{{/if}} {{$class.shortClassName}} extends \{{$class.fullClassName}}
    {
    {{* NOT USED ATM
    {{foreach  from=$class.constants item=constant}}
        {{$constant.docBlock}}
        const {{$constant.name}} = '{{$constant.value}}';
    {{/foreach}}
    {{foreach  from=$class.privateProperties item=property}}

    {{$property.docBlock|indent:4}}
        private {{if $property.isStatic}}static {{/if}}${{$property.name}} = {{if $property.value}}{{$property.value}}{{else}}null{{/if}};
    {{/foreach}}
    {{foreach  from=$class.protectedProperties item=property}}

    {{$property.docBlock|indent:4}}
        protected {{if $property.isStatic}}static {{/if}}${{$property.name}} = {{if $property.value}}{{$property.value}}{{else}}null{{/if}};
    {{/foreach}}
    {{foreach  from=$class.publicProperties item=property}}

    {{$property.docBlock|indent:4}}
        public {{if $property.isStatic}}static {{/if}}${{$property.name}} = {{if $property.value}}{{$property.value}}{{else}}null{{/if}};
    {{/foreach}}
    {{foreach  from=$class.publicMethods item=method}}

    {{$method.docBlock|indent:4}}
        {{if !$method.isAbstract}}public{{/if}} {{if $method.isStatic}}static {{/if}}{{if $method.isAbstract}}abstract {{/if}}function {{$method.name}}({{foreach from=$method.parameters item=parameter name=methodParameters}}{{include file="method-parameter-template.tpl" parameter=$parameter lastParameter=$smarty.foreach.methodParameters.last }}{{/foreach}}){{if $method.isAbstract || $class.isInterface }};{{/if}}

        {{if !$method.isAbstract &&  !$class.isInterface }}{
        // This is a method stub for IDE code hinting only
        }
        {{/if}}
    {{/foreach}}
    {{foreach  from=$class.protectedMethods item=method}}

    {{$method.docBlock|indent:4}}
        {{if !$method.isAbstract}}protected{{/if}} {{if $method.isStatic}}static {{/if}}{{if $method.isAbstract}}abstract {{/if}}function {{$method.name}}({{foreach from=$method.parameters item=parameter name=methodParameters}}{{include file="method-parameter-template.tpl" parameter=$parameter lastParameter=$smarty.foreach.methodParameters.last }}{{/foreach}}){{if $method.isAbstract || $class.isInterface }};{{/if}}

        {{if !$method.isAbstract &&  !$class.isInterface }}{
            // This is a method stub for IDE code hinting only
        }
        {{/if}}
    {{/foreach}}
    {{foreach  from=$class.privateMethods item=method}}

    {{$method.docBlock|indent:4}}
        {{if !$method.isAbstract}}private{{/if}} {{if $method.isStatic}}static {{/if}}{{if $method.isAbstract}}abstract {{/if}}function {{$method.name}}({{foreach from=$method.parameters item=parameter name=methodParameters}}{{include file="method-parameter-template.tpl" parameter=$parameter lastParameter=$smarty.foreach.methodParameters.last }}{{/foreach}}){{if $method.isAbstract || $class.isInterface }};{{/if}}

        {{if !$method.isAbstract &&  !$class.isInterface }}{
        // This is a method stub for IDE code hinting only
        }
        {{/if}}
    {{/foreach}}
    *}}

    }

