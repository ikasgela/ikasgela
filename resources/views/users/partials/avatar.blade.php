<img width="{{ $width }}" style="width:{{ $width }}px" src="{{ $user->avatar_url($width * 2) }}"
     alt="{{ $user->name }} {{ $user->surname }}"
     onerror="this.onerror=null;this.src='{{ url("/svg/missing_avatar.svg") }}';">
