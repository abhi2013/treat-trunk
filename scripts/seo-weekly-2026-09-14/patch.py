#!/usr/bin/env python3
"""Insert a snippet into post content at a unique anchor. Usage: patch.py <content_in> <snippet> <content_out> <post_id>"""
import re, sys
src, snip, out, pid = sys.argv[1:5]
c = open(src, encoding='utf-8').read()
s = open(snip, encoding='utf-8').read()
rules = {
  '41958': ('after',  r"energy can dip\.</p>"),
  '40571': ('before', r"<p>We hope that.s given you some ideas"),
  '37444': ('after',  r"tell us who they are for\.</p></div>\n<!-- /wp:html -->"),
  '41103': ('after',  r"Here.s why\.</p>"),
}
mode, pat = rules[pid]
m = list(re.finditer(pat, c))
assert len(m) == 1, f"{pid}: anchor matched {len(m)} times"
i = m[0].end() if mode == 'after' else m[0].start()
assert s.strip() not in c, f"{pid}: snippet already present"
new = c[:i] + ('\n' if mode == 'after' else '') + s + c[i:]
open(out, 'w', encoding='utf-8').write(new)
print(f"{pid}: inserted {len(s)} chars at offset {i}; {len(c)} -> {len(new)} bytes")
