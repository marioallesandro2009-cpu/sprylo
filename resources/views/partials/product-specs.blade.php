<div>
  <h2 style="font-size: var(--text-2xl); margin-bottom: var(--s5)">{{ $ui('ui_specs_title') }}</h2>
  <table style="width:100%; border-collapse: collapse; font-size: var(--text-sm)">
    <tbody>
      @foreach($specs as $spec)
        <tr @if(!$loop->last) style="border-bottom: 1px solid var(--rule)" @endif>
          <td style="padding: var(--s4) 0; color: var(--fg-mute); font-family: var(--ff-mono); font-size: var(--text-xs); letter-spacing: 0.06em; text-transform: uppercase; width: 40%">{{ $spec['label'] }}</td>
          <td style="padding: var(--s4) 0; font-weight: 600">{{ $spec['value'] }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
