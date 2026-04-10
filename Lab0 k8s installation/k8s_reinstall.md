# Kubernetes Cluster 重新安裝指南

本文件說明如何將現有的 Kubernetes Cluster 完全移除並重新安裝。

---

## 移除 Worker Node

在 **Master Node** 上先刪除 Worker Node

```bash
# Master Node
kubectl delete node <worker-node-name>
```

---

## kubeadm reset（**ALL Node**）

```bash
sudo kubeadm reset -f
```

---

## 清除殘留設定檔與網路介面（All Node）

```bash
# All Node
rm -rf $HOME/.kube
sudo rm -f /etc/cni/net.d/*
```

---

## 重新初始化 Cluster（Master Node）

```bash
# Master Node
sudo kubeadm init --pod-network-cidr=10.244.0.0/16
```

---

## 設定 kubeconfig（Master Node）

```bash
# Master Node
mkdir -p $HOME/.kube
sudo cp -i /etc/kubernetes/admin.conf $HOME/.kube/config
sudo chown $(id -u):$(id -g) $HOME/.kube/config
```

---

## 安裝 CNI Plugin（Master Node）

```bash
# Master Node
kubectl apply -f https://raw.githubusercontent.com/projectcalico/calico/master/manifests/calico.yaml
```

---

## Worker Node 加入 Cluster（Worker Node）

使用初始化時輸出的 join 指令：

```bash
# Worker Node
sudo kubeadm join 172.16.xx.xx:6443 --token XXXXXXX \
  --discovery-token-ca-cert-hash sha256:XXXXXXXXX
```

> 若 token 過期，可在 Master Node 重新產生：
>
> ```bash
> # Master Node
> kubeadm token create --print-join-command
> ```

---

## 驗證節點狀態（Master Node）

```bash
# Master Node
kubectl get node
kubectl get pods -A
```
