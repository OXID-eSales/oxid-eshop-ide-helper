
    {{if $class.isInterface}}interface{{elseif $class.isAbstract}}abstract class{{else}}class{{/if}} {{$class.shortClassName}} extends \{{$class.fullClassName}}
    {
    }

