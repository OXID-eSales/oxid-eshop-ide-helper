
{{if $class.namespace}}
    namespace {{$class.namespace}} {
    {{if $class.isInterface}}interface{{elseif $class.isAbstract}}abstract class{{else}}class{{/if}} {{$class.childClassName}} extends \{{$class.parentClassName}}
    {

    }
}
{{/if}}
