# 免费AI服务配置指南

为了让工作流完全免费运行，这里提供各种免费AI服务的替代方案和配置方法。

## 1. 文本生成服务（替代OpenAI）

### 1.1 Hugging Face Inference API（推荐）
**完全免费，有速率限制**

#### 配置步骤：
1. 访问 https://huggingface.co/settings/tokens
2. 创建新的Access Token
3. 选择推荐模型：
   - `microsoft/DialoGPT-large` - 对话生成
   - `google/flan-t5-large` - 通用文本生成
   - `facebook/blenderbot-400M-distill` - 内容处理

#### n8n配置：
```json
{
  "url": "https://api-inference.huggingface.co/models/google/flan-t5-large",
  "method": "POST",
  "headers": {
    "Authorization": "Bearer YOUR_HF_TOKEN",
    "Content-Type": "application/json"
  },
  "body": {
    "inputs": "{{ $json.prompt }}",
    "parameters": {
      "max_length": 1000,
      "temperature": 0.7
    }
  }
}
```

### 1.2 Google Gemini Free API
**每月有免费额度**

#### 配置步骤：
1. 访问 https://makersuite.google.com/app/apikey
2. 创建API密钥
3. 每月15次免费调用

#### n8n配置：
```json
{
  "url": "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent",
  "method": "POST",
  "headers": {
    "Content-Type": "application/json"
  },
  "query": {
    "key": "YOUR_GEMINI_API_KEY"
  },
  "body": {
    "contents": [
      {
        "parts": [
          {
            "text": "{{ $json.prompt }}"
          }
        ]
      }
    ]
  }
}
```

### 1.3 Ollama本地部署（完全免费）
**需要本地GPU，完全免费**

#### 安装步骤：
```bash
# 安装Ollama
curl -fsSL https://ollama.ai/install.sh | sh

# 下载模型
ollama pull llama2
ollama pull codellama
ollama pull mistral
```

#### n8n配置：
```json
{
  "url": "http://localhost:11434/api/generate",
  "method": "POST",
  "headers": {
    "Content-Type": "application/json"
  },
  "body": {
    "model": "llama2",
    "prompt": "{{ $json.prompt }}",
    "stream": false
  }
}
```

## 2. 图像生成服务

### 2.1 Hugging Face Diffusion Models（推荐）
**完全免费，有速率限制**

#### 推荐模型：
- `stabilityai/stable-diffusion-xl-base-1.0` - 高质量图像
- `runwayml/stable-diffusion-v1-5` - 经典模型
- `CompVis/stable-diffusion-v1-4` - 快速生成

#### n8n配置（已在工作流中）：
```json
{
  "url": "https://api-inference.huggingface.co/models/stabilityai/stable-diffusion-xl-base-1.0",
  "method": "POST",
  "headers": {
    "Authorization": "Bearer YOUR_HF_TOKEN",
    "Content-Type": "application/json"
  },
  "body": {
    "inputs": "{{ $json.prompt }}",
    "parameters": {
      "num_inference_steps": 30,
      "guidance_scale": 7.5,
      "width": 1024,
      "height": 576
    }
  }
}
```

### 2.2 Replicate免费额度
**每月有免费调用次数**

#### 配置步骤：
1. 访问 https://replicate.com
2. 获取API Token
3. 每月有一定免费额度

#### n8n配置：
```json
{
  "url": "https://api.replicate.com/v1/predictions",
  "method": "POST",
  "headers": {
    "Authorization": "Token YOUR_REPLICATE_TOKEN",
    "Content-Type": "application/json"
  },
  "body": {
    "version": "stability-ai/sdxl:39ed52f2a78e934b3ba6e2a89f5b1c712de7dfea535525255b1aa35c5565e08b",
    "input": {
      "prompt": "{{ $json.prompt }}",
      "width": 1024,
      "height": 576
    }
  }
}
```

## 3. 视频生成服务

### 3.1 RunwayML替代方案
**使用免费的图像到视频服务**

#### Runway Gen-3免费额度：
```json
{
  "url": "https://api.runwayml.com/v1/image_to_video",
  "method": "POST",
  "headers": {
    "Authorization": "Bearer YOUR_RUNWAY_TOKEN",
    "Content-Type": "application/json"
  },
  "body": {
    "image": "{{ $json.image_base64 }}",
    "seed": 42,
    "motion_score": 127
  }
}
```

### 3.2 Pika Labs（社区版）
**通过Discord机器人免费使用**

1. 加入Pika Labs Discord服务器
2. 使用`/animate`命令
3. 需要手动操作，不适合自动化

### 3.3 本地视频生成（完全免费）
**使用开源模型**

#### AnimateDiff本地部署：
```bash
# 克隆仓库
git clone https://github.com/guoyww/AnimateDiff.git
cd AnimateDiff

# 安装依赖
pip install -r requirements.txt

# 下载模型
# ... 按照项目说明下载模型文件
```

## 4. 语音合成服务

### 4.1 Coqui TTS（开源免费）
**本地部署，完全免费**

#### 安装配置：
```bash
# 安装Coqui TTS
pip install coqui-tts

# 启动API服务器
tts-server --model_name tts_models/en/ljspeech/tacotron2-DDC
```

#### n8n配置：
```json
{
  "url": "http://localhost:5002/api/tts",
  "method": "POST",
  "headers": {
    "Content-Type": "application/json"
  },
  "body": {
    "text": "{{ $json.text }}",
    "model_name": "tts_models/en/ljspeech/tacotron2-DDC"
  }
}
```

### 4.2 IBM Watson TTS免费额度
**每月有免费额度**

#### 配置步骤：
1. 注册IBM Cloud账户
2. 创建Text to Speech服务
3. 获取API密钥

#### n8n配置：
```json
{
  "url": "https://api.us-south.text-to-speech.watson.cloud.ibm.com/instances/YOUR_INSTANCE_ID/v1/synthesize",
  "method": "POST",
  "headers": {
    "Authorization": "Basic apikey:YOUR_IBM_API_KEY",
    "Content-Type": "application/json",
    "Accept": "audio/wav"
  },
  "body": {
    "text": "{{ $json.text }}",
    "voice": "en-US_AllisonV3Voice"
  }
}
```

### 4.3 Edge TTS（微软免费）
**通过edge-tts使用微软语音**

#### 安装使用：
```bash
# 安装edge-tts
pip install edge-tts

# 命令行使用
edge-tts --voice zh-CN-XiaoxiaoNeural --text "你好世界" --write-media hello.mp3
```

#### Python API封装：
```python
import edge_tts
import asyncio

async def generate_speech(text, voice="zh-CN-XiaoxiaoNeural"):
    communicate = edge_tts.Communicate(text, voice)
    await communicate.save("output.mp3")
```

## 5. 完全免费的工作流配置

### 替换方案组合：
1. **文本生成**：Hugging Face Inference API
2. **图像生成**：Hugging Face Stable Diffusion
3. **视频生成**：本地AnimateDiff或免费额度服务
4. **语音合成**：Coqui TTS本地部署

### 修改后的工作流节点配置：

#### AI信息提取节点（替换OpenAI）：
```json
{
  "url": "https://api-inference.huggingface.co/models/google/flan-t5-large",
  "method": "POST",
  "headers": {
    "Authorization": "Bearer YOUR_HF_TOKEN",
    "Content-Type": "application/json"
  },
  "body": {
    "inputs": "{{ $json.extraction_prompt }}",
    "parameters": {
      "max_length": 1000,
      "temperature": 0.3
    }
  }
}
```

#### 生成配音节点（替换Segmind）：
```json
{
  "url": "http://localhost:5002/api/tts",
  "method": "POST",
  "headers": {
    "Content-Type": "application/json"
  },
  "body": {
    "text": "{{ $json.narration }}",
    "model_name": "tts_models/zh-cn/baker/tacotron2-DDC-GST"
  }
}
```

## 6. 部署建议

### 本地GPU推荐配置：
- **GPU**: RTX 3060 12GB 或更高
- **内存**: 32GB RAM
- **存储**: 100GB+ SSD空间

### 云服务免费额度：
- **Google Colab**: 每天有免费GPU时间
- **Kaggle**: 每周30小时免费GPU
- **Hugging Face Spaces**: 免费CPU推理

### Docker容器部署：
```yaml
# docker-compose.yml
version: '3.8'
services:
  n8n:
    image: n8nio/n8n
    ports:
      - "5678:5678"
    volumes:
      - ~/.n8n:/home/node/.n8n
    environment:
      - N8N_BASIC_AUTH_ACTIVE=true
      - N8N_BASIC_AUTH_USER=admin
      - N8N_BASIC_AUTH_PASSWORD=password
  
  ollama:
    image: ollama/ollama
    ports:
      - "11434:11434"
    volumes:
      - ollama:/root/.ollama
  
  coqui-tts:
    image: coqui/tts
    ports:
      - "5002:5002"
    command: tts-server --model_name tts_models/en/ljspeech/tacotron2-DDC

volumes:
  ollama:
```

通过以上配置，你可以创建一个完全免费的视频生成工作流，只需要承担服务器运行成本。