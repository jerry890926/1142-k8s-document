# Lab1 - Pod / ReplicaSet / Deployment

## Architecture

![Architecture](img/2.png)

---

## Part 1 - Build Image

### 1.1 建立 Dockerfile

```bash
mkdir web
cd web
vim Dockerfile
```
![4.1](img/4_1.png)

### **[Dockerfile](web/Dockerfile)：**

```dockerfile
FROM php:8.2-apache
RUN mkdir -p /data && chmod 777 /data
COPY . /var/www/html/
```
![4.2](img/4_2.png)
### 1.2 建立 [index.php](web/index.php)

```bash
vim index.php
```
![index.php](img/5.png)

### 1.3 建立 [metrics.php](web/metrics.php)

```bash
vim metrics.php
```
![metrics.php](img/6.png)

### 1.4 建立 [stress.php](web/stress.php)

```bash
vim stress.php
```
![stress.php](img/7.png)

### 1.5 建立 [write.php](web/write.php)

```bash
vim write.php
```
![write.php](img/8.png)

### 1.6 Build & Push Image

```bash
docker build -t k8slab:lab1 .
docker tag k8slab:lab1 {repo}:lab1
docker push {repo}:lab1
```

> 將 `{repo}` 替換為你的 Docker Hub repository 名稱。

![build_image](img/9.png)
---

## Part 2 - 部署 Pod

![Pod 架構](img/11.png)

### 2.1 建立 Pod YAML

```bash
mkdir lab1
cd lab1
vim lab1-pod.yaml
```
![12.1](img/12_1.png)

### [lab1-pod.yaml](yaml/lab1-pod.yaml)
![12.2](img/12_2.png)

### 2.2 部署 Pod 並驗證

```bash
kubectl apply -f lab1-pod.yaml
kubectl get pod
kubectl get pod -o wide
```

### 2.3 Port Forward

```bash
kubectl port-forward pod/lab1-{學號}-pod 30081:80 --address 0.0.0.0
```
![13](img/13.png)

| 參數 | 說明 |
|---|---|
| `port-forward` | 轉發 port |
| `pod/lab1-{學號}-pod` | 轉發的資源/名稱 |
| `30081:80` | master / worker |
| `--address 0.0.0.0` | 同個網域下的流量都可存取（master / worker） |

### 2.4 驗證網頁

瀏覽器開啟：`http://{vm_ip}:30081/`

> **截圖一**（需要截圖網址、有學號的 pod）
![網頁截圖範例1](img/14.png)

### 2.5 更新 Pod 版本

開啟另一個 Terminal：

```bash
vim lab1-pod.yaml
# 更改 version 為 v2
kubectl apply -f lab1-pod.yaml
kubectl describe pod lab1-{學號}-pod
```

![15_1](img/15_1.png)

回到 web 刷新頁面（發生了什麼？）

```bash
kubectl delete pod lab1-{學號}-pod
kubectl apply -f lab1-pod.yaml
```

再次 port-forward & 回到 web 刷新頁面。

![15_2](img/15_2.png)

瀏覽器開啟：`http://{vm_ip}:30081/`

> **截圖二**（需要截圖網址、有學號的 pod）
![網頁截圖範例2](img/16.png)
---

## Part 3 - 部署 ReplicaSet

![ReplicaSet 架構](img/18.png)

### 3.1 建立 ReplicaSet YAML

```bash
vim lab1-replica.yaml
```
### [lab1-replica.yaml](yaml/lab1-replica.yaml)

![replicaset](img/19.png)

### 3.2 部署 ReplicaSet 並驗證

```bash
kubectl apply -f lab1-replica.yaml
kubectl get replicaset
kubectl get replicaset -o wide
kubectl get pod -o wide
kubectl port-forward rs/lab1-{學號}-rs 30081:80 --address 0.0.0.0
```
![20](img/20.png)

### 3.3 驗證網頁

瀏覽器開啟：`http://{vm_ip}:30081/`

> **截圖三**（需要截圖網址、有學號的 rs）
![網頁截圖範例3](img/21.png)

### 3.4 更新 ReplicaSet 版本

開啟另一個 Terminal：

```bash
vim lab1-replica.yaml
# 更改 version 為 v2
kubectl apply -f lab1-replica.yaml
kubectl describe rs lab1-{學號}-rs
```
![22](img/22.png)

回到 web 刷新頁面（發生了什麼？）

### 3.5 刪除 Pod 觀察自動重建

```bash
kubectl delete pod lab1-{學號}-rs-{pod_hash}
kubectl get pod -o wide
```
![23_1](img/23_1.png)

（發生了什麼？）

找出被 RS 自動產出的 pod，並且 port-forward 該 pod，回到 web 刷新頁面。

> **截圖四**（需要截圖網址、rs 產出新版本的 pod）
![網頁截圖範例4](img/23_2.png)

---

## Part 4 - 部署 Deployment

![Deployment 架構](img/25.png)

### 4.1 建立 Deployment YAML

```bash
vim lab1-deployment.yaml
```
### [lab1-deploy.yaml](yaml/lab1-deployment.yaml)

![deploy](img/26.png)

### 4.2 部署 Deployment 並驗證

```bash
kubectl apply -f lab1-deployment.yaml
kubectl get deployment -o wide
kubectl get pod -o wide
kubectl port-forward deploy/lab1-{學號}-deploy 30081:80 --address 0.0.0.0
```
![27](img/27.png)

### 4.3 驗證網頁

瀏覽器開啟：`http://{vm_ip}:30081/`

> **截圖五**（需要截圖網址、有學號的 deploy）
![網頁截圖範例5](img/28.png)

### 4.4 更新 Deployment 版本

開啟另一個 Terminal：

```bash
vim lab1-deployment.yaml
# 更改 version 為 v2
kubectl apply -f lab1-deployment.yaml
kubectl describe deploy lab1-{學號}-deploy
```
![29](img/29.png)

回到 web 刷新頁面（發生了什麼？）

### 4.5 觀察滾動更新

```bash
kubectl get pod -o wide
```
![30_1](img/30_1.png)


再次 port-forward deployment：

```bash
kubectl port-forward deploy/lab1-{學號}-deploy 30081:80 --address 0.0.0.0
```

回到 web 刷新頁面。

> **截圖六**（需要截圖網址、deploy 產出新版本的 pod）
![網頁截圖範例6](img/30_2.png)
