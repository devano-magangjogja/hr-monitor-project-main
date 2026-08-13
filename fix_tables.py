import os, re

ROOT = r"c:\laragon\www\hr-monitor-project\resources\views"

# Pola wrapper tabel yang perlu dibungkus overflow-x-auto
OLD = '<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">\n    <table class="w-full text-sm">'
NEW = '<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">\n    <div class="overflow-x-auto">\n    <table class="w-full text-sm min-w-[580px]">'

# Penutup: setelah </table> ada </div> penutup card
OLD_CLOSE = '    </tbody>\n    </table>\n</div>'
NEW_CLOSE = '    </tbody>\n    </table>\n    </div>\n</div>'

files_changed = []

for dirpath, dirnames, filenames in os.walk(ROOT):
    # Skip komponen yang sudah OK
    dirnames[:] = [d for d in dirnames if d not in ['vendor']]
    for fname in filenames:
        if not fname.endswith('.blade.php'):
            continue
        path = os.path.join(dirpath, fname)
        with open(path, 'r', encoding='utf-8') as f:
            content = f.read()

        if 'overflow-x-auto' in content:
            continue  # sudah ada, skip

        if OLD not in content:
            continue  # pola tidak cocok, skip

        new_content = content.replace(OLD, NEW)

        # Tutup div overflow-x-auto: ada berbagai pola penutup
        # Coba pola umum: </table>\n</div> diikuti bukan oleh konten tabel lain
        new_content = re.sub(
            r'([ \t]*</tbody>\n[ \t]*</table>)\n(</div>)',
            r'\1\n    </div>\n\2',
            new_content
        )

        if new_content != content:
            with open(path, 'w', encoding='utf-8') as f:
                f.write(new_content)
            rel = os.path.relpath(path, r"c:\laragon\www\hr-monitor-project")
            files_changed.append(rel)
            print(f"UPDATED: {rel}")
        else:
            print(f"NO_CHANGE: {fname}")

print(f"\nTotal files updated: {len(files_changed)}")
