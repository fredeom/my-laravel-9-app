{{ $message }}
<script>
  document.addEventListener('htmx:afterRequest', function (evt) {
    htmx.ajax('GET', '/products', {
      target: 'main'
    });
  }, {once: true});
</script>
