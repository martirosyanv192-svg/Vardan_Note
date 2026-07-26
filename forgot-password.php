<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Վերականգնել Գաղտնաբառը | Օնլայն Նոթատետր</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px 0;
        }
        
        .reset-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border: none;
            overflow: hidden;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #e1e1e1;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(118, 75, 162, 0.25);
            border-color: #764ba2;
        }

        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            color: white;
        }

        .brand-icon {
            font-size: 2.5rem;
            color: #764ba2;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card reset-card p-4 p-sm-5">
                
                <div class="text-center mb-4">
                    <i class="bi bi-key brand-icon"></i>
                    <h3 class="fw-bold mt-2">Մոռացե՞լ եք գաղտնաբառը</h3>
                    <p class="text-muted small">Մուտքագրեք ձեր էլ. փոստը, և մենք կուղարկենք վերականգնման հղումը</p>
                </div>

                <form>
                    <div class="mb-4">
                        <label for="email" class="form-label">Էլ. փոստ</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" placeholder="example@mail.com" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-custom w-100 mb-3">Ուղարկել հղումը</button>
                    
                    <div class="text-center">
                        <a href="login.php" class="text-decoration-none fw-bold small" style="color: #764ba2;">
                            <i class="bi bi-arrow-left me-1"></i>Վերադառնալ Մուտքի էջ
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>