export class Mensagem {
    id: number;
    texto: string;
    usuario_envio_id: number;
    usuario_recebimento_id: number;
    near: boolean;

    constructor(id: number, texto: string, usuario_envio_id: number, usuario_recebimento_id: number, near: boolean) {
        this.id = id;
        this.texto = texto;
        this.usuario_envio_id = usuario_envio_id;
        this.usuario_recebimento_id = usuario_recebimento_id;
        this.near = near;
    }

    static fromJson(json: any): Mensagem {
        return new Mensagem(json.id, json.texto, json.usuario_envio_id, json.usuario_recebimento_id, json.near);
    }
}