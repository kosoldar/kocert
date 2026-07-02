# Graph Report - .  (2026-06-24)

## Corpus Check
- 193 files · ~44,760 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 918 nodes · 1419 edges · 133 communities (84 shown, 49 thin omitted)
- Extraction: 98% EXTRACTED · 2% INFERRED · 0% AMBIGUOUS · INFERRED: 30 edges (avg confidence: 0.84)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- [[_COMMUNITY_API HTTP Controllers|API HTTP Controllers]]
- [[_COMMUNITY_Catalogo API & Responses|Catalogo API & Responses]]
- [[_COMMUNITY_User Management UI|User Management UI]]
- [[_COMMUNITY_Certificados List CRUD|Certificados List CRUD]]
- [[_COMMUNITY_Certificado Form Core|Certificado Form Core]]
- [[_COMMUNITY_Inspector Form Component|Inspector Form Component]]
- [[_COMMUNITY_Soldadores List Component|Soldadores List Component]]
- [[_COMMUNITY_User API Controller|User API Controller]]
- [[_COMMUNITY_Certificados Module|Certificados Module]]
- [[_COMMUNITY_Certificado Request Validation|Certificado Request Validation]]
- [[_COMMUNITY_Certificate Range Models|Certificate Range Models]]
- [[_COMMUNITY_Dashboard & Soldador API|Dashboard & Soldador API]]
- [[_COMMUNITY_Certificado API Controller|Certificado API Controller]]
- [[_COMMUNITY_Notification Service|Notification Service]]
- [[_COMMUNITY_CRUD List Pattern|CRUD List Pattern]]
- [[_COMMUNITY_Empresas List & Model|Empresas List & Model]]
- [[_COMMUNITY_Searchable Select Widget|Searchable Select Widget]]
- [[_COMMUNITY_App Module & Auth Interceptor|App Module & Auth Interceptor]]
- [[_COMMUNITY_Referencia Welding Data|Referencia Welding Data]]
- [[_COMMUNITY_Data Review & Empresa Form|Data Review & Empresa Form]]
- [[_COMMUNITY_Module 20|Module 20]]
- [[_COMMUNITY_Module 21|Module 21]]
- [[_COMMUNITY_Module 22|Module 22]]
- [[_COMMUNITY_Module 23|Module 23]]
- [[_COMMUNITY_Module 24|Module 24]]
- [[_COMMUNITY_Module 26|Module 26]]
- [[_COMMUNITY_Module 27|Module 27]]
- [[_COMMUNITY_Module 28|Module 28]]
- [[_COMMUNITY_Module 29|Module 29]]
- [[_COMMUNITY_Module 30|Module 30]]
- [[_COMMUNITY_Module 31|Module 31]]
- [[_COMMUNITY_Module 32|Module 32]]
- [[_COMMUNITY_Module 33|Module 33]]
- [[_COMMUNITY_Module 34|Module 34]]
- [[_COMMUNITY_Module 35|Module 35]]
- [[_COMMUNITY_Module 36|Module 36]]
- [[_COMMUNITY_Module 37|Module 37]]
- [[_COMMUNITY_Module 38|Module 38]]
- [[_COMMUNITY_Module 39|Module 39]]
- [[_COMMUNITY_Module 40|Module 40]]
- [[_COMMUNITY_Module 41|Module 41]]
- [[_COMMUNITY_Module 42|Module 42]]
- [[_COMMUNITY_Module 43|Module 43]]
- [[_COMMUNITY_Module 44|Module 44]]
- [[_COMMUNITY_Module 45|Module 45]]
- [[_COMMUNITY_Module 46|Module 46]]
- [[_COMMUNITY_Module 47|Module 47]]
- [[_COMMUNITY_Module 48|Module 48]]
- [[_COMMUNITY_Module 49|Module 49]]
- [[_COMMUNITY_Module 50|Module 50]]
- [[_COMMUNITY_Module 51|Module 51]]
- [[_COMMUNITY_Module 52|Module 52]]
- [[_COMMUNITY_Module 53|Module 53]]
- [[_COMMUNITY_Module 54|Module 54]]
- [[_COMMUNITY_Module 55|Module 55]]
- [[_COMMUNITY_Module 92|Module 92]]
- [[_COMMUNITY_Module 93|Module 93]]
- [[_COMMUNITY_Module 94|Module 94]]
- [[_COMMUNITY_Module 95|Module 95]]
- [[_COMMUNITY_Module 96|Module 96]]
- [[_COMMUNITY_Module 97|Module 97]]
- [[_COMMUNITY_Module 98|Module 98]]
- [[_COMMUNITY_Module 99|Module 99]]
- [[_COMMUNITY_Module 100|Module 100]]
- [[_COMMUNITY_Module 101|Module 101]]
- [[_COMMUNITY_Module 102|Module 102]]
- [[_COMMUNITY_Module 103|Module 103]]
- [[_COMMUNITY_Module 104|Module 104]]
- [[_COMMUNITY_Module 105|Module 105]]
- [[_COMMUNITY_Module 106|Module 106]]
- [[_COMMUNITY_Module 107|Module 107]]
- [[_COMMUNITY_Module 108|Module 108]]
- [[_COMMUNITY_Module 109|Module 109]]
- [[_COMMUNITY_Module 110|Module 110]]
- [[_COMMUNITY_Module 111|Module 111]]
- [[_COMMUNITY_Module 112|Module 112]]
- [[_COMMUNITY_Module 113|Module 113]]
- [[_COMMUNITY_Module 114|Module 114]]

## God Nodes (most connected - your core abstractions)
1. `CertificadoFormComponent` - 56 edges
2. `ToastService` - 31 edges
3. `Controller` - 23 edges
4. `CatalogoController` - 22 edges
5. `CertificadosListComponent` - 20 edges
6. `UsuariosListComponent` - 19 edges
7. `EmpresasListComponent` - 18 edges
8. `SoldadoresListComponent` - 18 edges
9. `NotificationService` - 17 edges
10. `SearchableSelectComponent` - 16 edges

## Surprising Connections (you probably didn't know these)
- `Empresas List Component Template` --semantically_similar_to--> `Soldadores List Component Template`  [INFERRED] [semantically similar]
  admin/src/app/empresas/list/empresas-list.component.html → admin/src/app/soldadores/list/soldadores-list.component.html
- `Inspector Form Component Template` --semantically_similar_to--> `Soldador Form Component Template`  [INFERRED] [semantically similar]
  admin/src/app/inspectores/form/inspector-form.component.html → admin/src/app/soldadores/form/soldador-form.component.html
- `Inspectores List Component Template` --semantically_similar_to--> `Soldadores List Component Template`  [INFERRED] [semantically similar]
  admin/src/app/inspectores/list/inspectores-list.component.html → admin/src/app/soldadores/list/soldadores-list.component.html
- `Usuarios List Component Template` --semantically_similar_to--> `Soldadores List Component Template`  [INFERRED] [semantically similar]
  admin/src/app/usuarios/list/usuarios-list.component.html → admin/src/app/soldadores/list/soldadores-list.component.html
- `Certificado Form Component Template` --conceptually_related_to--> `KoCert - Sistema de Certificacion de Soldadores`  [INFERRED]
  admin/src/app/certificados/form/certificado-form.component.html → admin/src/app/auth/login/login.component.html

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **CRUD Entity Management Pattern (list + form + review modal)** — concept_crud_list_pattern, concept_data_review_pattern, data_review_modal_component [INFERRED 0.90]
- **Certificado Form depends on Soldador, Empresa, Inspector entities** — certificado_form_component, soldador_form_component, empresa_form_component, inspector_form_component [EXTRACTED 1.00]
- **Admin Shell Layout wraps all feature routes via router-outlet** — layout_layout_component, admin_src_app_apphtml, admin_src_indexhtml [INFERRED 0.85]

## Communities (133 total, 49 thin omitted)

### Community 0 - "API HTTP Controllers"
Cohesion: 0.08
Nodes (16): Request, Request, Request, Request, Norma, Request, AuthController, EmpresaController (+8 more)

### Community 1 - "Catalogo API & Responses"
Cohesion: 0.11
Nodes (9): Collection, JsonResponse, Norma, Request, Certificado, Collection, CatalogoController, Posicion (+1 more)

### Community 2 - "User Management UI"
Cohesion: 0.08
Nodes (11): environment, UsuariosListComponent, UserRol, Usuario, UsuariosPage, CertificadosQuery, AdminNotification, NotificationColor (+3 more)

### Community 3 - "Certificados List CRUD"
Cohesion: 0.08
Nodes (9): CertificadosListComponent, CertEstado, Certificado, CertificadosPage, CertProgresion, CertResultado, CertTipo, CertTipoCupon (+1 more)

### Community 5 - "Inspector Form Component"
Cohesion: 0.10
Nodes (5): InspectorFormComponent, InspectoresModule, InspectoresListComponent, Inspector, InspectorService

### Community 6 - "Soldadores List Component"
Cohesion: 0.11
Nodes (5): SoldadoresListComponent, Soldador, SoldadoresPage, SoldadoresQuery, SoldadorService

### Community 7 - "User API Controller"
Cohesion: 0.12
Nodes (11): Request, UserController, Authenticatable, UserFactory, Factory, HasApiTokens, HasFactory, User (+3 more)

### Community 8 - "Certificados Module"
Cohesion: 0.17
Nodes (8): CertificadosModule, ConfirmDialogComponent, ModalStep, EmpresasModule, PaginationComponent, SoldadoresModule, SortHeaderComponent, UsuariosModule

### Community 9 - "Certificado Request Validation"
Cohesion: 0.13
Nodes (7): Validator, FormRequest, StoreCertificadoRequest, StoreSoldadorRequest, UpdateCertificadoRequest, UpdateSoldadorRequest, ValidaCombinacionesNorma

### Community 10 - "Certificate Range Models"
Cohesion: 0.12
Nodes (7): Model, CertificateRange, CertificateTest, DiameterRule, JointDesign, Material, ThicknessRule

### Community 11 - "Dashboard & Soldador API"
Cohesion: 0.24
Nodes (9): AnonymousResourceCollection, JsonResponse, Request, DashboardController, SoldadorController, Soldador, SoldadorResource, StoreSoldadorRequest (+1 more)

### Community 12 - "Certificado API Controller"
Cohesion: 0.27
Nodes (5): Certificado, Request, CertificadoController, StoreCertificadoRequest, UpdateCertificadoRequest

### Community 14 - "CRUD List Pattern"
Cohesion: 0.17
Nodes (11): Certificados List Component Template, CRUD List Pattern (search, paginate, sort, delete-confirm), DashboardComponent, Stats, UltimoCert, DashboardModule, routes, Empresas List Component Template (+3 more)

### Community 15 - "Empresas List & Model"
Cohesion: 0.19
Nodes (4): Empresa, EmpresasPage, EmpresaService, EmpresasQuery

### Community 17 - "App Module & Auth Interceptor"
Cohesion: 0.20
Nodes (4): AppModule, authInterceptor(), User, AuthService

### Community 18 - "Referencia Welding Data"
Cohesion: 0.14
Nodes (3): ReferenciaComponent, ReferenciaModule, routes

### Community 21 - "Module 21"
Cohesion: 0.44
Nodes (3): Certificado, Mpdf, PdfCertificadoService

### Community 23 - "Module 23"
Cohesion: 0.22
Nodes (4): AuthModule, routes, KoCert - Sistema de Certificacion de Soldadores, LoginComponent

### Community 24 - "Module 24"
Cohesion: 0.31
Nodes (6): CAMPO_LABELS, CertFormStep, TOP_LEVEL, SelectOption, CatalogoNorma, Norma

### Community 27 - "Module 27"
Cohesion: 0.42
Nodes (3): Request, ProcesoController, Proceso

### Community 28 - "Module 28"
Cohesion: 0.47
Nodes (9): Certificado Form Component Template, Data Review / Confirm Pattern, Multi-Step Form Pattern, Data Review Modal Component Template, Empresa Form Component Template, Inspector Form Component Template, Searchable Select Component Template, Soldador Form Component Template (+1 more)

### Community 31 - "Module 31"
Cohesion: 0.31
Nodes (4): LayoutModule, routes, Toast, ToastComponent

### Community 32 - "Module 32"
Cohesion: 0.50
Nodes (4): PageMeta, ToastType, SortDir, SortEvent

### Community 33 - "Module 33"
Cohesion: 0.39
Nodes (3): BelongsTo, HasMany, Norma

### Community 36 - "Module 36"
Cohesion: 0.38
Nodes (3): Seeder, GrupoBaseMetalConsumibleSeeder, QualifiedPositionSeeder

### Community 38 - "Module 38"
Cohesion: 0.47
Nodes (3): BelongsTo, HasMany, GrupoConsumible

### Community 41 - "Module 41"
Cohesion: 0.50
Nodes (3): AppRoutingModule, routes, authGuard()

### Community 42 - "Module 42"
Cohesion: 0.60
Nodes (3): Request, JsonResource, SoldadorResource

### Community 48 - "Module 48"
Cohesion: 1.00
Nodes (3): Validator, validarCombinaciones(), withValidator()

## Knowledge Gaps
- **47 isolated node(s):** `AppModule`, `routes`, `AppRoutingModule`, `App`, `AppModule` (+42 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **49 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Controller` connect `API HTTP Controllers` to `Catalogo API & Responses`, `User API Controller`, `Dashboard & Soldador API`, `Certificado API Controller`, `Module 27`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._
- **Why does `CatalogoController` connect `Catalogo API & Responses` to `API HTTP Controllers`?**
  _High betweenness centrality (0.046) - this node is a cross-community bridge._
- **Why does `CertificadoFormComponent` connect `Certificado Form Core` to `Module 34`, `Certificados List CRUD`, `Inspector Form Component`, `Certificados Module`, `Module 43`, `Data Review & Empresa Form`, `Module 24`, `Module 25`?**
  _High betweenness centrality (0.045) - this node is a cross-community bridge._
- **What connects `AppModule`, `routes`, `AppRoutingModule` to the rest of the system?**
  _48 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `API HTTP Controllers` be split into smaller, more focused modules?**
  _Cohesion score 0.0797872340425532 - nodes in this community are weakly interconnected._
- **Should `Catalogo API & Responses` be split into smaller, more focused modules?**
  _Cohesion score 0.10512820512820513 - nodes in this community are weakly interconnected._
- **Should `User Management UI` be split into smaller, more focused modules?**
  _Cohesion score 0.08095238095238096 - nodes in this community are weakly interconnected._