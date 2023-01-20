<x-profile :shareData="$shareData" pageTitle="{{$shareData['username']}}'s Followers">
  <div class="list-group">
    @foreach ($followers as $follow)
    <a href="/profile/{{$follow->UserDoingTheFollowing->username}}" class="list-group-item list-group-item-action">
          <img class="avatar-tiny" src="{{$follow->UserDoingTheFollowing->avatar}}" />
          {{$follow->UserDoingTheFollowing->username}}
      </a>
    @endforeach
</div>
  </x-profile>