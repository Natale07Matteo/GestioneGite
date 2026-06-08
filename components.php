/*
FILE DI ANGELO
*/
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vetrina - UI Components</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="vetrina.css">
    <script src="vetrina.js"></script>
    <style>
        .demo-section {
            padding: 2rem 0;
            border-bottom: 1px solid var(--my-white-h);
        }
        .demo-section h2 {
            color: var(--my-dark-blue);
            margin-bottom: 1.5rem;
        }
        .component-preview {
            padding: 2rem;
            border: 1px solid var(--my-white-h);
            border-radius: var(--radius-1);
            background: white;
            margin-bottom: 1rem;
        }
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
    </style>
</head>
<body>

<header>
    <div class="header-container header-left">
        <h2>Vetrina UI Kit</h2>
    </div>
    <div class="header-container header-right">
        <nav class="header-nav">
            <a href="index.html">Home</a>
            <a href="login.html">Login</a>
            <a href="register.html">Register</a>
        </nav>
    </div>
</header>

<div class="container" style="padding: 2rem; box-sizing: border-box;">

    <!-- COLORI -->
    <div class="demo-section">
        <h2>Color Palette</h2>
        <div class="color-palette">
            <div class="color" title="--my-blue"></div>
            <div class="color" title="--hex-red"></div>
            <div class="color" title="--hex-orange"></div>
            <div class="color" title="--hex-yellow"></div>
            <div class="color" title="--hex-dark-green"></div>
            <div class="color" title="--hex-light-green"></div>
            <div class="color" title="--hex-light-blue"></div>
            <div class="color" title="--hex-blue"></div>
            <div class="color" title="--hex-purple"></div>
        </div>
    </div>

    <!-- TIPOGRAFIA -->
    <div class="demo-section">
        <h2>Typography</h2>
        <div class="component-preview font-examples">
            <h1>Heading 1</h1>
            <h2>Heading 2</h2>
            <h3>Heading 3</h3>
            <h4>Heading 4</h4>
            <p>This is a paragraph example using the base font styles.</p>
        </div>
    </div>

    <!-- PULSANTI -->
    <div class="demo-section">
        <h2>Buttons</h2>
        <div class="component-preview">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; margin-bottom: 1rem;">
                <button class="xs">Button XS</button>
                <button class="s">Button S</button>
                <button class="m">Button M (Default)</button>
                <button class="l">Button L</button>
                <button class="xl">Button XL</button>
            </div>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                <button>Primary</button>
                <button class="cancel">Cancel</button>
                <button class="outline">Outline</button>
                <button disabled style="opacity: 0.5; cursor: not-allowed;">Disabled</button>
            </div>
        </div>
    </div>

    <!-- AVVISI -->
    <div class="demo-section">
        <h2>Alerts</h2>
        <div class="component-preview">
            <div class="alert alert-info">
                <p><strong>Info:</strong> This is an informational message.</p>
            </div>
            <div class="alert alert-success">
                <p><strong>Success:</strong> Operation completed successfully.</p>
            </div>
            <div class="alert alert-warning">
                <p><strong>Warning:</strong> Please check your inputs.</p>
            </div>
            <div class="alert alert-error">
                <p><strong>Error:</strong> Something went wrong.</p>
            </div>
        </div>
    </div>

    <!-- BADGE -->
    <div class="demo-section">
        <h2>Badges</h2>
        <div class="component-preview">
            <span class="badge badge-primary">Primary</span>
            <span class="badge badge-secondary">Secondary</span>
            <span class="badge badge-success">Success</span>
            <span class="badge badge-warning">Warning</span>
            <span class="badge badge-danger">Danger</span>
        </div>
    </div>

    <!-- CARD -->
    <div class="demo-section">
        <h2>Cards</h2>
        <div class="grid-3">
            <div class="card">
                <div class="card-header">
                    <h3>Card Title</h3>
                </div>
                <p>This is a basic card component with a header and body content.</p>
                <div class="card-footer">
                    <button class="s outline">Cancel</button>
                    <button class="s">Save</button>
                </div>
            </div>

            <div class="card centered">
                <h3>Centered Card</h3>
                <p>Content is centered here.</p>
                <button class="m full-width">Action</button>
            </div>
        </div>
    </div>

    <!-- INPUT -->
    <div class="demo-section">
        <h2>Form Inputs</h2>
        <div class="component-preview inputs">
            <div class="form-group text-input-20">
                <label>Text Input</label>
                <input type="text" placeholder="Enter text...">
            </div>
            
            <div class="form-group text-input-20">
                <label>Select</label>
                <select>
                    <option>Option 1</option>
                    <option>Option 2</option>
                    <option>Option 3</option>
                </select>
            </div>

            <div class="break"></div>

            <div class="form-group text-input-full">
                <label>Full Width Input</label>
                <input type="text" placeholder="Full width...">
            </div>

            <div class="form-group text-input-full">
                <label>Textarea</label>
                <textarea rows="3" placeholder="Enter long text..."></textarea>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="demo-check">
                <label for="demo-check">Checkbox Option</label>
            </div>
        </div>
    </div>

    <!-- TABELLE -->
    <div class="demo-section">
        <h2>Tables</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Mario Rossi</td>
                        <td>mario@example.com</td>
                        <td><span class="badge badge-success">Active</span></td>
                        <td><button class="xs">Edit</button></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Luigi Verdi</td>
                        <td>luigi@example.com</td>
                        <td><span class="badge badge-warning">Pending</span></td>
                        <td><button class="xs">Edit</button></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Peach Toadstool</td>
                        <td>peach@example.com</td>
                        <td><span class="badge badge-danger">Inactive</span></td>
                        <td><button class="xs">Edit</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODALI -->
    <div class="demo-section">
        <h2>Modals</h2>
        <div class="component-preview">
            <button class="m" onclick="openModal('demo-modal')">Open Demo Modal</button>
        </div>
    </div>

    <!-- PAGINE -->
    <div class="demo-section">
        <h2>Pagine Template</h2>
        <div class="grid-3">
            <div class="card">
                <div class="card-header">
                    <h3>Home / Dashboard</h3>
                </div>
                <p>Pagina principale con header, sidebar (opzionale) e contenuto.</p>
                <div class="card-footer">
                    <a href="index.html" class="button m full-width">Vai alla Dashboard</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Login</h3>
                </div>
                <p>Pagina di accesso con form e pulsante Portale Calvino.</p>
                <div class="card-footer">
                    <a href="login.html" class="button m full-width">Vai al Login</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Registrazione</h3>
                </div>
                <p>Pagina di registrazione nuovo utente.</p>
                <div class="card-footer">
                    <a href="register.html" class="button m full-width">Vai alla Registrazione</a>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Template Modale -->
<div id="demo-modal" class="modal-overlay hidden">
    <div class="modal">
        <div class="modal-header">
            <h3>Modal Title</h3>
        </div>
        <div class="modal-body">
            <p>This is a modal window. You can put any content here.</p>
            <p>It includes a header, body, and footer section.</p>
        </div>
        <div class="modal-footer">
            <button class="s cancel" onclick="closeModal('demo-modal')">Close</button>
            <button class="s" onclick="closeModal('demo-modal')">Save Changes</button>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

</body>
</html>
