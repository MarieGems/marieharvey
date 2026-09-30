# Marie Harvey site notes

- Blog posts: the body wrapper must be `<div class="post-content" data-reveal>` (copy an existing post). `_shared/site.css` forces `.post-content[data-reveal]` visible, because tall blocks never hit the reveal observer's visibility threshold on phones and would stay at opacity:0.
- Never add `data-reveal` to a very tall container without that override; it makes text invisible (but still copyable).
- When editing `_shared/site.css` or `_shared/premium2.js`, bump the `?v=` on every page so browsers reload it.
