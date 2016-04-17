<?php

final class ArcanistFreeBSDPortsLinter extends ArcanistLinter {

  const LINT_ERROR = 1;
  const PORTS_ROOT = 'ports';

  private $parsedPaths = array();

  public function getInfoName() {
    return pht('FreeBSD Ports Lint');
  }

  public function getInfoDescription() {
    return pht('Arcanist wrapper for portlint.');
  }

  public function getLinterName() {
    return 'FREEBSDPORTS';
  }

  public function getLinterConfigurationName() {
    return 'freebsd_ports';
  }

  public function lintPath($path) {
    $resolved_ports_root = realpath($this->getProjectRoot().'/'.
      self::PORTS_ROOT).'/';
    $path = realpath($path);
    $plen = strlen($resolved_ports_root);

    // print('Ports are in: '.$resolved_ports_root."\n");
    // print('Matching path: '.$path."\n");
    $port_path = null;
    if (strncmp($path, $resolved_ports_root, $plen) == 0) {
      $port = substr($path, $plen);
      $port_dir_arr = explode('/', $port);

      if (count($port_dir_arr) > 2) {
        $portname = implode('/', array_slice($port_dir_arr, 0, 2));
        if (!isset($this->parsedPaths[$portname])) {
          $this->parsedPaths[$portname] = true;
          $port_path = $resolved_ports_root.$portname;
          $this->lintPortPath($port_path);
        }
      }
    }
  }

  private function lintPortPath($path) {

    $output = null;
    $ret = null;

    exec('portlint '.escapeshellarg($path).' 2>&1', $output, $ret);
    if ($ret >= 127) {
      $this->lintMessage($path, 'FATAL', 'Fatal error', 'calling portlint',
        ArcanistLintSeverity::SEVERITY_ERROR);
      return;
    }

    foreach ($output as $line) {
      $matches = null;

      if (preg_match('/^FATAL:(.*)/', $line, $matches)) {
        $this->lintMessage($path, 'PORTLINT_FATAL', 'portlint fatal error', $matches[1],
          ArcanistLintSeverity::SEVERITY_ERROR);
      }

      if (preg_match('/^WARN:(.*)/', $line, $matches)) {
        $this->lintMessage($path, 'PORTLINT_WARN', 'portlint warning', $matches[1],
          ArcanistLintSeverity::SEVERITY_ADVICE);
      }
    }
  }

  private function lintMessage($path, $code, $name, $desc, $severity) {
    $message = id(new ArcanistLintMessage())
      ->setPath($path)
      ->setCode($code)
      ->setName(pht($name))
      ->setDescription(trim($desc))
      ->setSeverity($severity);

    $this->addLintMessage($message);
  }
}

?>
