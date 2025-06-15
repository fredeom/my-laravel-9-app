{{ $message }}
<script>
  document.addEventListener('htmx:afterRequest', function (evt) {
    htmx.ajax('GET', '/orders', {
      target: 'main'
    });
  }, {once: true});
</script>
