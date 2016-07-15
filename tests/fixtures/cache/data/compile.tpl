{@include a_file.here}
{@for(some as thing)}
  {@if(thing=='somebody')}
    SomeBody
  {@else}
    NoBody
  {@endif}
{@endfor}
with {nothing} special