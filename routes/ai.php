<?php

use Laravel\Mcp\Facades\Mcp;

// MCP Server para gestión del LMS (Organizaciones, Periodos, Categorías y Cursos)
Mcp::web('/mcp/lms', \App\Mcp\Servers\LmsServer::class)->middleware(['auth:sanctum']);
