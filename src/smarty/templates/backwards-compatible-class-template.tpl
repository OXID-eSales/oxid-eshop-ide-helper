    /** @deprecated since v6.0.0. This class will be remove in the future. Please use the corresponding class from the unified namespace. */
    {{if $class.isInterface}}interface{{elseif $class.isAbstract}}abstract class{{else}}class{{/if}} {{$class.childClassName}} extends \{{$class.parentClassName}}
    {

    }
