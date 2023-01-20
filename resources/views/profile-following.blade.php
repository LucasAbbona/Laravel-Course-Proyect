<x-profile :shareData="$shareData" pageTitle="{{$shareData['username']}}'s Follows">
  <div class="list-group">
    @foreach ($following as $follow)
    <a href="/profile/{{$follow->UserBeingTheFollowed->username}}" class="list-group-item list-group-item-action">
        <img class="avatar-tiny" src="{{$follow->UserBeingTheFollowed->avatar}}" />
        {{$follow->UserBeingTheFollowed->username}}
      </a>
    @endforeach
</div>
  </x-profile>