import io, os, subprocess, glob

files = sorted(glob.glob('tests/Feature/**/*.php', recursive=True)) + sorted(glob.glob('tests/Unit/*.php'))
files = [f for f in files if not f.endswith('TestCase.php')]

env = dict(os.environ)
env['XDEBUG_MODE'] = 'off'

failed = []
for f in files:
    cls = os.path.splitext(os.path.basename(f))[0]
    p = subprocess.run(
        ['php', '-d', 'memory_limit=2048M', 'artisan', 'test', '--filter', cls],
        capture_output=True, text=True, env=env, timeout=900,
    )
    out = (p.stdout or '') + (p.stderr or '')
    if 'memory size' in out.lower():
        status = 'MEMORY'
    elif '"result":"passed"' in out or 'OK (' in out:
        status = 'PASS'
    else:
        status = 'FAIL'
    if status != 'PASS':
        failed.append((f, status, out[-2500:]))
    print('%-8s %s' % (status, cls))

print('\n==== PROBLEM FILES ====')
for f, s, out in failed:
    print('\n####', f, s)
    print(out)
