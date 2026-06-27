<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* database/import/index.twig */
class __TwigTemplate_db573eb7821ef2499ad3c3e126794e6bc7ea116e95905459f1970832b2bf4b41 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "import.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $this->parent = $this->loadTemplate("import.twig", "database/import/index.twig", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        echo twig_escape_filter($this->env, twig_sprintf(_gettext("Importing into the database \"%s\""), ($context["db"] ?? null)), "html", null, true);
    }

    public function getTemplateName()
    {
        return "database/import/index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  46 => 3,  35 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "database/import/index.twig", "E:\\xampp74\\htdocs\\rsbr\\pengajuan_cuti\\phpMyAdmin\\templates\\database\\import\\index.twig");
    }
}
