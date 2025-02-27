export class User {
    id: number;
    nome: string;
    latitude: string;
    longitude: string;

    constructor(id: number, nome: string, latitude: string, longitude: string) {
        this.id = id;
        this.nome = nome;
        this.latitude = latitude;
        this.longitude = longitude;
    }

    static fromJson(json: any): User {
        return new User(json.id, json.nome, json.latitude, json.longitude);
    }
}